<div class="modal fade" id="modalImport">

    <div class="modal-dialog">

        <form
            action="{{ route('barang.import') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Import Data Barang
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>


                <div class="modal-body">

                    <div class="form-group">

                        <label>
                            File Excel
                        </label>

                        <input
                            type="file"
                            name="file"
                            class="form-control"
                            accept=".xlsx,.xls"
                            required>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Batal

                    </button>


                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="fas fa-file-import"></i>
                        Import

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
