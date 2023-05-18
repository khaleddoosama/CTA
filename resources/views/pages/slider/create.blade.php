@extends('master')
@section('title')
    انشاء سلايدر
@endsection
@section('content')
    <style>
        #sliderForm1 .error {
            color: red;
            display: inline-block;
        }
    </style>
    <!-- main body -->
    <div class="row">


        @if ($errors->any())
            <div class="error">{{ $errors->first('Name') }}</div>
        @endif



        <div class="col-xl-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <br><br>

                    <div class="col-xl-12 mb-30">
                        <div class="card card-statistics h-100">
                            <div class="card-body">
                                <form id="sliderForm1" method="post" class="form-horizontal"
                                    action="{{ route('slider.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label class="control-label" for="title">عنوان السلايدر</label>
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="title" name="title" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="desc">وصف السلايدر</label>
                                        <div class="mb-2">
                                            <textarea name="description" id="desc" rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="image">صورة السلايدر</label>
                                        <div class="mb-2">
                                            <input type="file" class="form-control" id="image" name="image" />
                                            <img src="" alt="" id="showImage">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">انشاء
                                            السلايدر</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // when the user fill image input must show the image bottom of the input with js native
        document.getElementById('image').onchange = function() {
            var reader = new FileReader();

            reader.onload = function(e) {
                // get loaded data and render thumbnail.
                document.getElementById("showImage").src = e.target.result;
                // give the image width and height 100px with js native
                document.getElementById("showImage").style.width = "200px";
                document.getElementById("showImage").style.height = "200px";
            };

            // read the image file as a data URL.
            reader.readAsDataURL(this.files[0]);
        };
    </script>
@endsection
