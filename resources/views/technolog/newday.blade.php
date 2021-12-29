@extends('layouts.app')

@section('content')




<!-- EDIT -->
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-warning">Saqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- EDIT -->


<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="exampleModals" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-danger">Ok</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->


<!-- EDD -->
<div class="modal fade" id="exampleModalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="kingarden">
                    <label for="basic-url" class="form-label">MTM nomi</label>
                    <select class="form-select" aria-label="Default select example">
                        <option selected>12-MTM</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>

                <div class="input-group mb-3 mt-3">
                    <span class="input-group-text" id="inputGroup-sizing-default">3-4 yosh</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="inputGroup-sizing-default">4-7 yosh</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-info text-white">Saqlash</button>
            </div>
        </div>
    </div>
</div>

<!-- EDD -->
<div class="py-4 px-4">
    <div class="box-sub" style="
        display: flex;
        justify-content: flex-end;">
        <input type="submit" class="btn btn-success text-white mb-2" value="SAQLASH">
    </div>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th style="width: 14px;">
                    <input type="checkbox" id="select-all">
                </th>
                <th colspan="3">
                    <select style="width: 32%;
                                    border: navajowhite;
                                    padding: 7px 4px;
                                    background-color: #555;
                                    color: #fff;
                                    display: inline-flex;
                                    border-radius: 3px;
                                    box-sizing: border-box;">
                        <option value="1-menu">1-menyu</option>
                        <option value="1-menu">1-menyu</option>
                        <option value="1-menu">1-menyu</option>
                    </select>
                </th>
                <th>
                    <select style="width: 100%;
                                    border: navajowhite;
                                    padding: 7px 4px;
                                    background-color: #80afc6;
                                    color: #fff;
                                    display: inline-flex;
                                    border-radius: 3px;
                                    box-sizing: border-box;">
                        <option value="1-menu">1-menyu</option>
                        <option value="1-menu">1-menyu</option>
                        <option value="1-menu">1-menyu</option>
                    </select>
                </th>

                <th> <button class="btn btn-info p-0" style="
                    padding: 3px 16px !important;" data-bs-toggle="modal" data-bs-target="#exampleModalsadd"> <i class="fas fa-plus-square text-white "></i></button> </th>
            </tr>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">MTT-nomi</th>
                <th scope="col">3-4 yosh</th>
                <th scope="col">4-7 yosh</th>
                <th scope="col">Qisqa guruh</th>
                <th style="width: 70px;">Edit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row"><input type="checkbox" id="bike" name="vehicle" value="gentra"></th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
                <td>@mdo</td>
                <td><i class="far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" style="cursor: pointer; margin-right: 16px;"> </i><i class="fas fa-trash-alt text-danger" data-bs-toggle="modal" data-bs-target="#exampleModals" style="cursor: pointer;"></i></td>
            </tr>


        </tbody>
    </table>
</div>


@endsection

@section('script')
<script>
    document.getElementById('select-all').onclick = function() {
        var checkboxes = document.getElementsByName('vehicle');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
</script>

@endsection