@extends('master')
@section('title')
    ٍسلايدر
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

                    <a class="button x-small" href="{{ route('slider.create') }}">
                        اضافة سلايدر
                    </a>
                    <br><br>

                    <div class="table-responsive">
                        <table id="datatable" class="table p-0 table-hover table-sm table-bordered" data-page-length="50"
                            style="text-align: center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الوصف</th>
                                    <th>الصورة</th>
                                    <th>الحاله</th>
                                    <th>الاجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($sliders as $slider)
                                    <tr>
                                        <td>{{ $slider->id }}</td>
                                        <td>{{ $slider->title }}</td>
                                        <td>{{ $slider->description }}</td>
                                        <td>
                                            <img src="{{ asset('uploads/sliders/' . $slider->image) }}"
                                                alt="{{ $slider->name }}" width="50px" height="50px"
                                                class="rounded-circle">
                                        </td>
                                        <td>
                                            @if ($slider->status == 1)
                                                <span class="badge badge-success">مفعل</span>
                                            @else
                                                <span class="badge badge-danger">غير مفعل</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-info btn-sm" href="{{ route('slider.edit', $slider->id) }}"
                                                title="تعديل"><i class="fa fa-edit"></i></a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#delete{{ $slider->id }}" title="حذف"><i
                                                    class="fa fa-trash"></i></button>
                                            {{-- change status --}}
                                            <a href="{{ route('slider.status', $slider->id) }}"
                                                class="btn btn-sm {{ $slider->status == 1 ? 'btn-danger' : 'btn-success' }}">
                                                {{ $slider->status == 1 ? 'الغاء التفعيل' : 'تفعيل' }}
                                        </td>
                                    </tr>
                                    <!-- delete_modal_Grade -->
                                    <div class="modal fade" id="delete{{ $slider->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                                                        id="exampleModalLabel">
                                                        حذف سلايدر
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('slider.destroy', 'test') }}" method="post">
                                                        {{ method_field('Delete') }}
                                                        @csrf
                                                        <h5>هل انت متاكد من عملية الحذف ؟</h5>
                                                        <input id="id" type="hidden" name="id"
                                                            class="form-control" value="{{ $slider->id }}">
                                                        <input type="text" class="form-control"
                                                            value="{{ $slider->title }}" name="name" required readonly>

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
