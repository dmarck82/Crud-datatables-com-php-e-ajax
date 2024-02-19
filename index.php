<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/datatables.min.css" rel="stylesheet">


    <title>Server Side DataTable CRUD operations</title>
</head>

<body>
    <h1 class="text-center">Datatable CRUD</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <button type="button" style="margin-bottom: 40px" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            Add User
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <table id="datatable" class="table">
                            <thead>
                                <th>SNo.</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>City</th>
                                <th>Options</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-1.13.8/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


    <script type="text/javascript">
        // AÇÃO PARA CARREGAR A TABELA
        $('#datatable').DataTable({
            'serverSide': true,
            'processing': true,
            'paging': true,
            'order': [],
            'ajax': {
                'url': 'fetch_data.php',
                'type': 'post',
            },
            'fnCreatedRow': function(nRow, aData, iDataIndex) {
                $(nRow).attr('id', aData[0]);
            },
            'columnDefs': [{
                'target': [0, 5],
                'orderable': false,
            }]
        });

        // AÇÃO PARA SALVAR O FORMULARIO DO MODAL
        $(document).on('submit', '#saveUserForm', function(event) {
            event.preventDefault();
            var name = $('#inputUsername').val();
            var email = $('#inputEmail').val();
            var mobile = $('#inputMobile').val();
            var city = $('#inputCity').val();
            if (name != '' && email != '' && mobile != '' && city != '') {
                $.ajax({
                    url: "add_user.php",
                    data: {
                        name: name,
                        email: email,
                        mobile: mobile,
                        city: city
                    },
                    type: 'post',
                    success: function(data) {
                        var json = JSON.parse(data);
                        status = json.status;
                        if (status == 'success') {
                            table = $('#datatable').DataTable();
                            table.draw();
                            alert('Usuário adicionado com sucesso!');
                            $('#inputUsername').val('');
                            $('#inputEmail').val('');
                            $('#inputMobile').val('');
                            $('#inputCity').val('');
                            $('#addUserModal').modal('hide');
                        }
                    }
                });
            } else {
                alert("Please fill all the required fields.");
            }
        });

        // AÇÃO AO CLICAR NO BOTÃO DE EDITAR
        $(document).on('click', '.editBtn', function(event) {
            var id = $(this).data('id');
            var trid = $(this).closest('tr').attr('id');
            $.ajax({
                url: "get_single_user.php",
                data: {
                    id: id
                },
                type: 'post',
                success: function(data) {
                    var json = JSON.parse(data);
                    $('#id').val(json.id);
                    $('#trid').val(trid);
                    $('#_inputUsername').val(json.username);
                    $('#_inputEmail').val(json.email);
                    $('#_inputMobile').val(json.mobile);
                    $('#_inputCity').val(json.city);
                    $('#editUserModal').modal('show')
                }

            })
        });

        // AÇÃO AO CLICAR NO BOTAO DE SALVAR QUANDO ESTIVER EDITANDO UM USUARIO
        $(document).on('submit', '#updateUserForm', function(event) {
            var id = $('#id').val();
            var trid = $('#trid').val();
            var username = $('#_inputUsername').val();
            var email = $('#_inputEmail').val();
            var mobile = $('#_inputMobile').val();
            var city = $('#_inputCity').val();
            $.ajax({
                url: "update_user.php",
                data: {
                    id: id,
                    username: username,
                    email: email,
                    mobile: mobile,
                    city: city
                },
                type: 'post',
                success: function(data) {
                    var json = JSON.parse(data);
                    status = json.status;
                    if (status == 'success') {
                        table = $('#datatable').DataTable();
                        var button = '<a href="javascript:void();" data-id="' + id + '" class="btn btn-sm btn-info editBtn">Edit</a> <a href="javascript:void(); data-id="' + id + '" class="btn btn-sm btn-danger btnDelete">Delete</a>'
                        var row = table.row("[id='" + trid + "']");
                        row.row("[id='" + trid + "']").data([id, username, email, mobile, city, button]);
                        $('#editUserModal').modal('hide');
                    } else {
                        alert('failed');
                    }
                }

            })
        });

        // AÇÃO AO CLICAR NO BOTAO DE DELETAR
        $(document).on('click', '.btnDelete', function(event) {
            var id = $(this).data('id');
            if (confirm('Are you sure want to delete this user?')) {
                $.ajax({
                    url: "delete_user.php",
                    data: {
                        id: id
                    },
                    type: 'post',
                    success: function(data) {
                        var json = JSON.parse(data);
                        var status = json.status;

                        if (status == 'success') {
                            $('#' + id).closest('tr').remove();
                        } else {
                            alert('Failed.');
                        }
                    }
                });
            }
        });
    </script>

    <!-- iNICIO ADD USER MODAL -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exempleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="saveUserForm" action="javascript:void();" method="post">
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label for="inputUsername" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputUsername" name="username" value="">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputEmail" name="inputEmail">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputMobile" class="col-sm-2 col-form-label">Mobile</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputMobile" name="inputMobile">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputCity" class="col-sm-2 col-form-label">City</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputCity" name="inputCity">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- FIM ADD USER MODAL -->

    <!-- iNICIO EDIT USER MODAL -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="exempleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateUserForm" action="javascript:void();" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id" value="">
                        <input type="hidden" id="trid" name="trid" value="">
                        <div class="mb-3 row">
                            <label for="inputUsername" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="_inputUsername" name="_username" value="">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="_inputEmail" name="_inputEmail">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputMobile" class="col-sm-2 col-form-label">Mobile</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="_inputMobile" name="_inputMobile">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputCity" class="col-sm-2 col-form-label">City</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="_inputCity" name="_inputCity">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- FIM EDIT USER MODAL -->

</body>

</html>