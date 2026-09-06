<style>
    .exam-requirements-table {
        width: 100%;
        table-layout: fixed;
    }

    .exam-requirements-table .document-link-column {
        width: 37%;
    }

    .exam-requirements-table .document-number-column {
        width: 4%;
    }

    .exam-requirements-table .document-name-column {
        overflow-wrap: anywhere;
        width: 32%;
    }

    .exam-requirements-table .document-status-column {
        width: 8%;
    }

    .exam-requirements-table .document-action-column {
        width: 7%;
    }

    .exam-requirements-table .document-note-column,
    .exam-requirements-table .document-file-column {
        width: 6%;
    }

    .exam-requirements-table .document-compact-column {
        text-align: center;
        white-space: nowrap;
    }

    .exam-requirements-table .document-link-input {
        box-sizing: border-box;
        display: block;
        max-width: none;
        min-width: 0;
        width: 100% !important;
    }

    .exam-document-save-toolbar {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-top: 12px;
    }

    @media (max-width: 767px) {
        .exam-document-save-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .exam-document-save-toolbar .btn {
            margin-top: 10px;
            width: 100%;
        }
    }
</style>
