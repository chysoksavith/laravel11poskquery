@extends('layouts.app')
@section('admin_content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Pos</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Pos
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="cart mb-4">
                        <div class="card-header">
                            @include('components.categoryHeader', ['categories' => $categories])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flashMessage alert alert-success" style="display: none"></div>
@endsection
