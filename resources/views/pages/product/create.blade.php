@extends('master')
@section('title')
    انشاء منتج
@endsection
@section('content')

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
                                <form id="signupForm" method="post" class="form-horizontal"
                                    action="{{ route('product.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label class="control-label" for="name">اسم المنتج</label>
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="name" name="name" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="code">كود المنتج</label>
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="code" name="code" />
                                        </div>
                                    </div>
                                    {{-- select category --}}
                                    <div class="form-group">
                                        <label class="mr-sm-2" for="inlineFormCustomSelect">اختر القسم</label>
                                        <select class="custom-select mr-sm-2" id="inlineFormCustomSelect"
                                            name="category_id">
                                            <option selected value="">اختر القسم</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="desc">وصف المنتج</label>
                                        <div class="mb-2">
                                            <textarea name="description" id="desc" rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="sell">سعر الشراء</label>
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="sell"
                                                name="selling_price" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="discount">سعر الخصم</label>
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="discount"
                                                name="discount_price" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="quantity">الكمية</label>
                                        <div class="mb-2">
                                            <input type="text" class="form-control" id="quantity" name="quantity" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="image">صورة المنتج</label>
                                        <div class="mb-2">
                                            <input type="file" class="form-control" id="image" name="image" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">انشاء
                                            منتج</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
