<?php

namespace App\Services;

use RuntimeException;

class DosenSignatureImageService
{
    const CANVAS_WIDTH = 420;
    const CANVAS_HEIGHT = 150;
    const CANVAS_PADDING = 10;
    const MAX_SOURCE_PIXELS = 12000000;

    /**
     * Standardize a lecturer signature without changing the ink itself.
     *
     * The output is a transparent PNG with a consistent canvas. White space
     * around a scanned/uploaded signature is removed before it is centered.
     *
     * @param string $contents
     * @return string
     */
    public function normalize($contents)
    {
        $source = $this->decodeImage($contents);

        try {
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $bounds = $this->findInkBounds($source, $sourceWidth, $sourceHeight);

            if ($bounds === null) {
                throw new RuntimeException('Tanda tangan belum memiliki goresan yang dapat digunakan.');
            }

            $contentWidth = $bounds['right'] - $bounds['left'] + 1;
            $contentHeight = $bounds['bottom'] - $bounds['top'] + 1;
            $cropped = $this->transparentCanvas($contentWidth, $contentHeight);
            imagecopy($cropped, $source, 0, 0, $bounds['left'], $bounds['top'], $contentWidth, $contentHeight);
            $this->makeWhitespaceTransparent($cropped);

            $targetWidth = self::CANVAS_WIDTH - (self::CANVAS_PADDING * 2);
            $targetHeight = self::CANVAS_HEIGHT - (self::CANVAS_PADDING * 2);
            $scale = min($targetWidth / $contentWidth, $targetHeight / $contentHeight);
            $renderedWidth = max(1, (int) round($contentWidth * $scale));
            $renderedHeight = max(1, (int) round($contentHeight * $scale));
            $offsetX = (int) floor((self::CANVAS_WIDTH - $renderedWidth) / 2);
            $offsetY = (int) floor((self::CANVAS_HEIGHT - $renderedHeight) / 2);

            $canvas = $this->transparentCanvas(self::CANVAS_WIDTH, self::CANVAS_HEIGHT);
            imagecopyresampled(
                $canvas,
                $cropped,
                $offsetX,
                $offsetY,
                0,
                0,
                $renderedWidth,
                $renderedHeight,
                $contentWidth,
                $contentHeight
            );
            $this->makeWhitespaceTransparent($canvas);

            ob_start();
            imagepng($canvas, null, 6);
            $normalized = ob_get_clean();
            imagedestroy($canvas);
            imagedestroy($cropped);

            if (!is_string($normalized) || $normalized === '') {
                throw new RuntimeException('Tanda tangan tidak dapat diproses menjadi PNG.');
            }

            return $normalized;
        } finally {
            imagedestroy($source);
        }
    }

    /**
     * Return non-sensitive image metrics for a signature audit.
     *
     * @param string $contents
     * @return array
     */
    public function inspect($contents)
    {
        $source = $this->decodeImage($contents);

        try {
            $width = imagesx($source);
            $height = imagesy($source);
            $bounds = $this->findInkBounds($source, $width, $height);

            if ($bounds === null) {
                throw new RuntimeException('Tanda tangan belum memiliki goresan yang dapat digunakan.');
            }

            $contentWidth = $bounds['right'] - $bounds['left'] + 1;
            $contentHeight = $bounds['bottom'] - $bounds['top'] + 1;
            $imageInfo = @getimagesizefromstring($contents);

            return [
                'mime' => is_array($imageInfo) && isset($imageInfo['mime']) ? $imageInfo['mime'] : 'unknown',
                'width' => $width,
                'height' => $height,
                'content_width' => $contentWidth,
                'content_height' => $contentHeight,
                'content_ratio' => round(($contentWidth * $contentHeight) / ($width * $height), 4),
                'left_margin' => $bounds['left'],
                'top_margin' => $bounds['top'],
                'right_margin' => $width - $bounds['right'] - 1,
                'bottom_margin' => $height - $bounds['bottom'] - 1,
                'opaque_background_pixels' => $this->countOpaqueWhitespace($source, $width, $height),
            ];
        } finally {
            imagedestroy($source);
        }
    }

    /**
     * Determine whether an image has already been normalized by this service.
     *
     * @param array $metrics
     * @return bool
     */
    public function hasStandardCanvas(array $metrics)
    {
        return ($metrics['mime'] ?? '') === 'image/png'
            && (int) ($metrics['width'] ?? 0) === self::CANVAS_WIDTH
            && (int) ($metrics['height'] ?? 0) === self::CANVAS_HEIGHT
            && (int) ($metrics['opaque_background_pixels'] ?? 1) === 0
            && (int) ($metrics['left_margin'] ?? 0) >= self::CANVAS_PADDING - 1
            && (int) ($metrics['right_margin'] ?? 0) >= self::CANVAS_PADDING - 1
            && (int) ($metrics['top_margin'] ?? 0) >= self::CANVAS_PADDING - 1
            && (int) ($metrics['bottom_margin'] ?? 0) >= self::CANVAS_PADDING - 1;
    }

    /**
     * @param string $contents
     * @return resource
     */
    protected function decodeImage($contents)
    {
        if (!extension_loaded('gd')) {
            throw new RuntimeException('Ekstensi pengolah gambar GD belum tersedia di server.');
        }

        if (!is_string($contents) || $contents === '') {
            throw new RuntimeException('Berkas tanda tangan tidak tersedia.');
        }

        $imageInfo = @getimagesizefromstring($contents);
        if (!is_array($imageInfo) || empty($imageInfo[0]) || empty($imageInfo[1])) {
            throw new RuntimeException('Berkas tanda tangan bukan gambar yang valid.');
        }

        if (((int) $imageInfo[0] * (int) $imageInfo[1]) > self::MAX_SOURCE_PIXELS) {
            throw new RuntimeException('Dimensi gambar tanda tangan terlalu besar untuk diproses.');
        }

        $image = @imagecreatefromstring($contents);
        if ($image === false) {
            throw new RuntimeException('Format gambar tanda tangan tidak dapat diproses.');
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * @param resource $image
     * @param int $width
     * @param int $height
     * @return array|null
     */
    protected function findInkBounds($image, $width, $height)
    {
        $bounds = null;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (!$this->isInkPixel($image, $x, $y)) {
                    continue;
                }

                if ($bounds === null) {
                    $bounds = ['left' => $x, 'top' => $y, 'right' => $x, 'bottom' => $y];
                    continue;
                }

                $bounds['left'] = min($bounds['left'], $x);
                $bounds['top'] = min($bounds['top'], $y);
                $bounds['right'] = max($bounds['right'], $x);
                $bounds['bottom'] = max($bounds['bottom'], $y);
            }
        }

        return $bounds;
    }

    /**
     * @param int $width
     * @param int $height
     * @return resource
     */
    protected function transparentCanvas($width, $height)
    {
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $transparent);

        return $image;
    }

    /**
     * @param resource $image
     * @return void
     */
    protected function makeWhitespaceTransparent($image)
    {
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        $width = imagesx($image);
        $height = imagesy($image);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (!$this->isInkPixel($image, $x, $y)) {
                    imagesetpixel($image, $x, $y, $transparent);
                }
            }
        }
    }

    /**
     * @param resource $image
     * @param int $width
     * @param int $height
     * @return int
     */
    protected function countOpaqueWhitespace($image, $width, $height)
    {
        $count = 0;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($this->isInkPixel($image, $x, $y)) {
                    continue;
                }

                $color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
                if ((int) (isset($color['alpha']) ? $color['alpha'] : 0) < 120) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Treat transparent and nearly-white pixels as background while retaining
     * black, blue, and other handwritten signature colors.
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @return bool
     */
    protected function isInkPixel($image, $x, $y)
    {
        $color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
        $alpha = isset($color['alpha']) ? (int) $color['alpha'] : 0;

        if ($alpha >= 120) {
            return false;
        }

        return !(
            $color['red'] >= 242
            && $color['green'] >= 242
            && $color['blue'] >= 242
        );
    }
}
