@extends('master')
@section('title')
    المنتجات
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

                    <a class="button x-small" href="{{ route('product.create') }}">
                        اضافة منتج
                    </a>
                    <br><br>

                    <div class="table-responsive">
                        <table id="datatable" class="table p-0 table-hover table-sm table-bordered" data-page-length="50"
                            style="text-align: center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>القسم</th>
                                    <th>الكود</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>الصورة</th>
                                    <th>الحاله</th>
                                    <th>الاجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        @if ($product->category_id == null)
                                            <td>لا يوجد قسم</td>
                                        @else
                                            <td>{{ $product->category->name }}</td>
                                        @endif
                                        <td>{{ $product->code }}</td>
                                        <td>{{ $product->selling_price }}</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td>
                                            <img src="{{ asset('uploads/products/' . $product->image) }}"
                                                alt="{{ $product->name }}" width="50px" height="50px"
                                                class="rounded-circle">
                                        </td>
                                        <td>
                                            @if ($product->status == 1)
                                                <span class="badge badge-success">مفعل</span>
                                            @else
                                                <span class="badge badge-danger">غير مفعل</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a class="btn btn-info btn-sm" href="{{ route('product.edit', $product->id) }}"
                                                title="تعديل"><i class="fa fa-edit"></i></a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#delete{{ $product->id }}" title="حذف"><i
                                                    class="fa fa-trash"></i></button>
                                            {{-- change status --}}
                                            <a href="{{ route('product.status', $product->id) }}"
                                                class="btn btn-sm {{ $product->status == 1 ? 'btn-danger' : 'btn-success' }}">
                                                {{ $product->status == 1 ? 'الغاء التفعيل' : 'تفعيل' }}
                                        </td>
                                    </tr>
                                    <!-- delete_modal_Grade -->
                                    <div class="modal fade" id="delete{{ $product->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                                                        id="exampleModalLabel">
                                                        حذف المنتج
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('product.destroy', 'test') }}" method="post">
                                                        {{ method_field('Delete') }}
                                                        @csrf
                                                        <h5>هل انت متاكد من عملية الحذف ؟</h5>
                                                        <input id="id" type="hidden" name="id"
                                                            class="form-control" value="{{ $product->id }}">
                                                        <input type="text" class="form-control"
                                                            value="{{ $product->name }}" name="name" required readonly>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal"> اغلاق</button>
                                                            <button type="submit" class="btn btn-danger"> حذف</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
