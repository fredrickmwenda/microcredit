@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.client_excel_import',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Import Client Data</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">Import Clients</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" id="app">

        <div class="card">
            <div class="card-header">
                Import Client Data from Excel
            </div>
            <div class="card-body p-10">
                <div class="flex justify-center items-center" id="loading">
                    <p>Importing...Please wait</p>
                    <div class="spinner-border animate-spin inline-block w-8 h-8 border-4 rounded-full" role="status">
                      <span class="visually-hidden">...</span>
                    </div>
                </div>

                <form action="{{ route('client_import') }}" method="POST" enctype="multipart/form-data" id="importform">
                    @csrf
                    <p><b>Example: Excel Data will be on following format.</b></p>
                    <table class="table table-striped table-hover table-condensed bg-gray-100" id="data-table">
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                Account Number
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    Mr John
                                </td>
                                <td>
                                    01586894565
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Mr Doe
                                </td>
                                <td>
                                    0158458965
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <div class="custom-file text-left">
                            <input type="file" name="file" class="custom-file-input" id="customFile"  accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="getFileData(this);">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <button class="btn btn-primary" id="import_data">Import data</button>
                </form>
                
            </div>
            
           
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        function getFileData(myFile){
                var file = myFile.files[0];  
                var filename = file.name;
                $('.custom-file-label').text(filename);
            }
        $(document).ready( function() {
            $('#loading').hide();
            $('#import_data').click(function() {
                $('#loading').show();
                $('#importform').hide();
            });
        });


        
    </script>
@endsection
