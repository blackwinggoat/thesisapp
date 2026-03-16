<!-- BEGIN FOOTER -->
<footer>
    &copy; 2024 <a href="#fakelink">Universitas Muslim Indonesia</a><br />
    Design by <a href="http://isohdesign.com" target="_blank">Fakultas Ilmu Komputer</a>.
</footer>
<!-- END FOOTER -->


</div><!-- /.page-content -->
</div><!-- /.wrapper -->
<!-- END PAGE CONTENT -->



<!-- BEGIN BACK TO TOP BUTTON -->
<div id="back-top">
    <a href="#top"><i class="fa fa-chevron-up"></i></a>
</div>
<!-- END BACK TO TOP -->
@include("tugasakhir.components.modal")



<!--
		===========================================================
		END PAGE
		===========================================================
		-->

<!--
===========================================================
Placed at the end of the document so the pages load faster
===========================================================
-->
<!-- MAIN JAVASRCIPT (REQUIRED ALL PAGE)-->
<script src="{{ asset('master/assets/js/jquery.min.js')}}"></script>
<script src="{{ asset('master/assets/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/retina/retina.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/nicescroll/jquery.nicescroll.js')}}"></script>
<script src="{{ asset('master/assets/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/backstretch/jquery.backstretch.min.js')}}"></script>

<!-- PLUGINS -->
<script src="{{ asset('master/assets/plugins/skycons/skycons.js')}}"></script>
<script src="{{ asset('master/assets/plugins/prettify/prettify.js')}}"></script>
<script src="{{ asset('master/assets/plugins/magnific-popup/jquery.magnific-popup.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/owl-carousel/owl.carousel.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/chosen/chosen.jquery.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/icheck/icheck.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{ asset('master/assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
<script src="{{ asset('master/assets/plugins/mask/jquery.mask.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/validator/bootstrapValidator.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/datatable/js/bootstrap.datatable.js')}}"></script>
<script src="{{ asset('master/assets/plugins/summernote/summernote.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/markdown/markdown.js')}}"></script>
<script src="{{ asset('master/assets/plugins/markdown/to-markdown.js')}}"></script>
<script src="{{ asset('master/assets/plugins/markdown/bootstrap-markdown.js')}}"></script>
<script src="{{ asset('master/assets/plugins/slider/bootstrap-slider.js')}}"></script>

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js "></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>


<script src="{{ asset('master/assets/plugins/toastr/toastr.js')}}"></script>

<!-- FULL CALENDAR JS -->
<script src="{{ asset('master/assets/plugins/fullcalendar/lib/jquery-ui.custom.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/fullcalendar/fullcalendar/fullcalendar.min.js')}}"></script>
<script src="{{ asset('master/assets/js/full-calendar.js')}}"></script>

<!-- EASY PIE CHART JS -->
<script src="{{ asset('master/assets/plugins/easypie-chart/easypiechart.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/easypie-chart/jquery.easypiechart.min.js')}}"></script>

<!-- KNOB JS -->
<!--[if IE]>
<script type="text/javascript" src="{{ asset('master/assets/plugins/jquery-knob/excanvas.js')}}"></script>
<![endif]-->
<script src="{{ asset('master/assets/plugins/jquery-knob/jquery.knob.js')}}"></script>
<script src="{{ asset('master/assets/plugins/jquery-knob/knob.js')}}"></script>

<!-- FLOT CHART JS -->
<script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.js')}}"></script>
<script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.tooltip.js')}}"></script>
<script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.resize.js')}}"></script>
<script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.selection.js')}}"></script>
<script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.stack.js')}}"></script>
<script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.time.js')}}"></script>

<!-- MORRIS JS -->
<script src="{{ asset('master/assets/plugins/morris-chart/raphael.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/morris-chart/morris.min.js')}}"></script>
<script src="{{ asset('master/assets/plugins/morris-chart/example.js')}}"></script>

<!-- C3 JS -->
<script src="{{ asset('master/assets/plugins/c3-chart/d3.v3.min.js')}}" charset="utf-8"></script>
<script src="{{ asset('master/assets/plugins/c3-chart/c3.min.js')}}"></script>

<!-- MAIN APPS JS -->
<script src="{{ asset('master/assets/js/apps.js')}}"></script>
<script src="{{ asset('js/axios.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.10.12/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('master/assets/plugins/validator/example.js')}}"></script>
<script>
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

    let token = document.head.querySelector("meta[name='csrf-token']");

    if (token) {
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
    } else {
        console.error("CSRF token not found!");
    }
</script>
@yield("script")
<script>
    window.onload = () => {
        document.getElementById("Ocontainer").style.display = "none";
    }
</script>

</body>
</html>
