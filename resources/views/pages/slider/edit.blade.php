@extends('master')
@section('title')
    تعديل سلايدر
@endsection
@section('content')
    <style>
        #sliderForm2 .error {
            color: red;
            display: inline-block;
        }
    </style>
    <!-- main body -->
    <div class="row">


        @if ($errors->any())
            <div class="error">{{ $errors->first('Name') }}</div>
        @endif



        <div class="col-xl-12 mb-30 ">
            <div class="card card-statistics h-100">
                <div class="card-body row ">

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

                    <div class="col-xl-10 mb-30">
                        <div class="card card-statistics h-100">
                            <div class="card-body">
                                <form id="sliderForm2" method="post" class="form-horizontal"
                                    action="{{ route('slider.update', $slider->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label class="control-label" for="name">اسم السلايدر</label>
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="name" name="title"
                                                value="{{ $slider->title }} " />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="desc">وصف السلايدر</label>
                                        <div class="mb-2">
                                            <textarea name="description" id="desc" rows="5" class="form-control">{{ $slider->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="image">صورة السلايدر</label>
                                        <div class="mb-2">
                                            <input type="file" class="form-control" id="image" name="image" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">تعديل
                                            سلايدر</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-30">
                        <img src="{{ asset('uploads/sliders/' . $slider->image) }}" alt="image"
                            style="width: 200px; height: 200px" class="rounded-circle" id="showImage" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // when the user fill image input must show the image bottom of the input with js native
        document.getElementById('image').onchange = function() {
            // check if empty 
            if (this.value == "") {
                document.getElementById("showImage").src = "{{ asset('uploads/sliders/' . $slider->image) }}";
                return;
            }
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
