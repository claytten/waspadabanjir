@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'subscribers',
  'title' => 'Subscribers',
  'first_title' => 'Subscribers',
  'first_link' => route('admin.subscribers.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('header-right')
<div class="col-lg-6 col-5 text-right">
  @if(auth()->user()->can('subscriber-create'))
    <a href="javascript:void(0)" class="btn btn-sm btn-neutral" onclick="resetForm()" data-toggle="modal" data-target="#modal-add-subscribers">New</a>
  @endif
  @if (auth()->user()->getRoleNames()[0] === 'Super Admin' || auth()->user()->id === (!empty($cacheSub) ? $cacheSub->id : null))
    <a href="javascript:void(0)" class="btn btn-sm btn-neutral" data-toggle="modal" data-target="#modal-send-multiple">Broadcasting</a>
  @endif
</div>
@endsection

@section('content_body')
<div class="row">
  <div class="col">
    <div class="card">
      <!-- Card header -->
      <div class="card-header">
        <h3 class="mb-0">Subscribers Management</h3>
      </div>
      <div class="table-responsive py-4">
        <table class="table table-flush" id="subscribeTable">
          <thead class="thead-light">
            <tr>
              <th>No</th>
              <th>Name</th>
              <th>Phone Number</th>
              <th>Address</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>No</th>
              <th>Name</th>
              <th>Phone Number</th>
              <th>Address</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </tfoot>
          <tbody>
            @foreach($subscribers as $index => $item)
                <tr id="rows_{{ $item->id }}">
                  <th>{{ $index +1 }}</th>
                  <td>{{ ucwords($item->name) }}</td>
                  <td>{{ $item->phone }}</td>
                  <td>
                    {{$item->regency->name}},
                    {{$item->regency->province->name}}</td>
                  @if ($item->status)
                    <td><button type="button" class="btn btn-success btn-sm" onclick="changeStatus('{{$item->id}}', '{{$item->status}}', '{{ $index+1 }}')">Active</button></td>
                  @else
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="changeStatus('{{$item->id}}', '{{$item->status}}', '{{ $index+1 }}')" >Non-Active</button></td>
                  @endif
                  <td>
                    @if(auth()->user()->can('subscriber-edit'))
                      <button 
                        onclick="editSubscribe('{{ $item['id']}}', '{{ $item['name'] }}', '{{ $item['phone'] }}', '{{ $item['status']}}', '{{ $index+1 }}')" 
                        type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-add-subscribers">
                        Edit
                      </button>
                    @endif
                    @if(auth()->user()->can('subscriber-delete'))
                      <button onclick="deleteSubscribe('{{ $item->id }}')"  type="button" class="btn btn-danger btn-sm">Delete</button>
                    @endif
                    @if (auth()->user()->getRoleNames()[0] === 'Super Admin' || auth()->user()->id === (!empty($cacheSub) ? $cacheSub->id : null))
                      <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-send-personal" onclick="$('#personal_phone_to').val('{{ $item->phone }}')">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                      </button>
                    @endif
                  </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Create Subscribers --}}
<div class="modal fade" id="modal-add-subscribers" tabindex="-1" role="dialog" aria-labelledby="modal-add-subscribers" aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
            <div class="text-center text-muted mb-4">
                <small id="form_title">Add Subscribers</small>
            </div>
            <form id="addSubscribeForm">
              <input type="hidden" name="id" id="id" value="">
              <input type="hidden" id="idEdit" value="">
              <input type="hidden" name="_method" id="_method" value="POST">
              @csrf
              <div class="form-group">
                <label class="form-control-label" for="name">Ful Name</label>
                <input class="form-control @error('name') is-invalid @enderror" placeholder="Your name" type="text" name="name" value="{{ old('name') }}" id="name">
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
              </div>
              <div class="form-group">
                <label class="form-control-label" for="phone">Phone</label>
                <input class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" id="phone" placeholder="example (+62xx/08xx)" onblur="phoneNumber(this)" onfocus="phoneNumber(this)" onchange="phoneNumber(this)" onkeyup="phoneNumber(this)">
                @error('phone')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
              <div class="form-group">
                <div class="input-group input-group-merge input-group-alternative">
                  <label class="form-control-label" for="provinces">Province</label>
                  <select onchange="searchProvince()" id="provinces" class="form-control" data-toggle="select">
                    <option value="" disabled selected>--Select Province--</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="input-group input-group-merge input-group-alternative">
                  <label class="form-control-label" for="regencies">Regency</label>
                  <select id="regencies" class="form-control" data-toggle="select" name="address">
                    <option value="" disabled selected>--Select Regency--</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="input-group input-group-merge input-group-alternative">
                  <label class="form-control-label" for="status">Status</label>
                  <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" data-toggle="select">
                    <option value="" disabled selected>--Select Status--</option>
                    <option value="1">Active</option>
                    <option value="0">Non-Active</option>
                  </select>
                </div>
              </div>
              
              <div class="text-center">
                <button type="submit" class="btn btn-primary my-4 btn-add-subscriber">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Send Message Personal --}}
<div class="modal fade" id="modal-send-personal" tabindex="-1" role="dialog" aria-labelledby="modal-send-personal" aria-hidden="true">
  <div class="modal-dialog modal modal-dialog-centered modal-lg" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-4 py-lg-4">
            <div class="row">
              <div class="col-lg-12">
                <div class="text-center text-muted mb-4">
                  <small id="form_title">Send Personal Message</small>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text">from</span>
                        </div>
                        <input class="form-control" style="padding-left: 10px" placeholder="Your phone from" type="text" name="personal_phone_from" id="personal_phone_from" value="Admin" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text">to</span>
                        </div>
                        <input class="form-control" style="padding-left: 10px" placeholder="Your phone to" type="text" name="personal_phone_to" id="personal_phone_to" readonly>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <textarea class="form-control" style="padding-left: 10px" placeholder="Body Message" name="personal_body_message" id="personal_body_message"></textarea>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="text-center">
                  <button type="button" class="btn btn-primary my-4" onclick="sendPersonalMessage()">Submit</button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Send Message Multiple --}}
<div class="modal fade" id="modal-send-multiple" tabindex="-1" role="dialog" aria-labelledby="modal-send-multiple" aria-hidden="true">
  <div class="modal-dialog modal modal-dialog-centered modal-lg" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-4 py-lg-4">
            <div class="row">
              <div class="col-lg-12">
                <div class="text-center text-muted mb-4">
                  <small id="form_title">Regency Broadcast Message</small>
                </div>
              </div>

              <div class="col-lg-12" id="section-from">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text">from</span>
                        </div>
                        <input class="form-control" style="padding-left: 10px" placeholder="Your phone from" type="text" name="multiple_phone_from" id="multiple_phone_from" value="Admin" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <select name="multiple_phone" id="multiple_phone" class="form-control" data-toggle="select" onchange="changeSenders(this)">
                          <option value="" disabled selected>--Select Senders--</option>
                          <option value="all">All</option>
                          <option value="regency">By Regency</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <textarea class="form-control" style="padding-left: 10px" placeholder="Body Message" name="multiple_body_message" id="multiple_body_message"></textarea>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="text-center">
                  <button type="button" class="btn btn-primary my-4" onclick="sendMultipleMessage()">Submit</button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('plugins_js')
<script type="text/javascript" src="{{ asset('vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $("#provinces, #regencies, #status").select2({width: "100%"});
    $('#multiple_phone, #multiple_phone_regency').select2();
    $('#regencies').prop('disabled', true);

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.provinces.index') }}",
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#provinces').append(`
              <option value="${value['id']}">${value['name']}
              </option>`);
          });
        } else {
          console.log("data trouble");
        }
      }
    });
  });

  const subscribeTable = $("#subscribeTable").DataTable({
      lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
      language: {
        "emptyTable": "Please select sort or search data"
      },
      pageLength: 5,
      columnDefs: [
        {
          target: 5,
          orderable: false,
          searchable: false
        }
      ],
      responsive: true,
  });

  const phoneRegex = /^(^\+62|62|^08)(\d{3,4}-?){2}\d{3,4}$/;

  function phoneNumber(input) {
    let inputPhone = input.value;

    if(phoneRegex.test(inputPhone)) {
      $('#phone').removeClass('is-invalid').next('span').remove();
    } else {
      $('#phone').next('span').remove();
      $('#phone').addClass('is-invalid').after(`
        <span class="invalid-feedback" role="alert">
            <strong>Please Check again your phone number (+62xx/08xx)</strong>
        </span>
      `);
    }
  }

  function deleteSubscribe(id){
    Swal.fire({
        title: 'Are you sure?',
        text: "This user status will be set to Destroy, and this data delete anymore!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if(result.value){
          $.post("{{ route('admin.subscribers.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE'}, function(result){
            // Append Alert Result
            subscribeTable.row("#rows_"+id).remove().draw();
            Swal.fire(
              result.status,
              result.message,
              'success'
            );
          }).fail(function(jqXHR, textStatus, errorThrown){
            Swal.fire({
              icon: 'error',
              title: 'Oops... ' + textStatus,
              text: 'Please Try Again or Refresh Page!'
            });
          });
        }
    });
  }

  function changeStatus(id, no) {
    Swal.fire({
      title: 'Do you want to change status?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: `Active`,
      confirmButtonColor: '#2dce89',
      denyButtonText: `Non-Active`,
      denyButtonColor: '#f5365c',
    }).then((result) => {
      if (result.isConfirmed) {
        ajaxStatus(id, no, 1);
      } else if (result.isDenied) {
        ajaxStatus(id, no, 0);
      }
    })
  }

  function ajaxStatus(id, no, status) {
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.subscribers.index') }}?id=" + id + '&status=' + status,
      type : "GET",
      dataType : "json",
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Something went wrong!'
        });
      },
      success:function(result) {
        if(result) {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Status Has been Changed To '+ (result.data['status'] === "1" ? 'Active' : 'Non-Active') +'',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            console.log(typeof result.data['status'], result.data['status']);
            const updateData = [
              no,
              result.data['name'],
              result.data['phone'],
              `${result.address['regency_name']}, ${result.address['province_name']},`,
              '<button type="button" class="btn btn-'+ (result.data['status'] === "1" ? 'success' : 'danger')+' btn-sm" onclick="changeStatus('+id+', '+no+')">'+ (result.data['status'] === "1" ? 'Active' : 'Non-Active') +'</button>',
              addActionOption(id, result.data['name'], result.data['phone'], result.data['status'], no),
            ];
            subscribeTable.row($("#rows_"+id)).data(updateData);
          });
        } else {
          console.log("data trouble");
        }
      }
    });
  }

  function addActionOption(id,name, phone, status, idEdit) {
    let result = '';
    let setName = "'"+name+"'";
    @if(auth()->user()->can('subscriber-edit')) {
      result += '<button onclick="editSubscribe('+id+', '+setName+', '+phone+', '+status+', '+idEdit+')"  type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-add-subscribers">Edit</button>';
    }
    @endif

    @if(auth()->user()->can('subscriber-delete')) {
      result += '<button onclick="deleteSubscribe('+id+')"  type="button" class="btn btn-danger btn-sm">Delete</button>';
    }
    @endif

    @if (auth()->user()->getRoleNames()[0] === 'Super Admin' || auth()->user()->id === (!empty($cacheSub) ? $cacheSub->id : null))
      result += `
        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-send-personal" onclick="$('#personal_phone_to').val('${phone}')">
          <i class="fa fa-paper-plane" aria-hidden="true"></i>
        </button>
      `;
    @endif

    result = result.replaceAll(':id', id);

    return result;
  }

  function editSubscribe(id,name,phone,status, idEdit) {
    $("#id").val(id);
    $("#idEdit").val(idEdit);
    $("#_method").val('PUT');
    $("#name").val(name);
    $('#phone').val(phone);
    $('#status').val(status).change();
    $('#regencies').val("").change();
    $('#regencies').empty();
    $('#regencies').append('<option value="" disabled selected>--Select Regency--</option>');
    $('#regencies, #districts, #villages').prop('disabled', true);

    $("#form_title").text('Update Subscribe');
    $(".btn-add-subscriber").text("Update");
  }

  $("#addSubscribeForm").submit(function(e){
    e.preventDefault();

    $("input").removeClass('is-invalid');
    $(".invalid-feedback").remove();

    if(!$('#name').val() || !phoneRegex.test($('#phone').val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Please check again your input form!'
      });
    } else {
      const id = $("#id").val();
      let link = "";
      const idEdit = $('#idEdit').val();
      if($("#_method").val() == "POST"){
          if(!$('#regencies').val()) {
            return Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Please check again your input form!'
            });
          }
          link = "{{ route('admin.subscribers.store') }}";
      } else {
          link = '{{ route('admin.subscribers.update', ':id') }}';
          link = link.replace(':id', id);
      }

      $.post(link, $(this).serialize(), function(result){
        console.log(result);
        const rows = (subscribeTable.rows().count() == 0) ? "1" : subscribeTable.row(':last').data()[0];
        if($("#_method").val() == "POST"){
          // Store
          subscribeTable.row.add([
            parseInt(rows)+1,
            result.data['name'],
            result.data['phone'],
            `${result.address['regency_name']}, ${result.address['province_name']}`,
            '<button type="button" class="btn btn-'+ (result.data['status'] === "1" ? 'success' : 'danger')+' btn-sm" onclick="changeStatus('+result.data['id']+', '+parseInt(rows)+1+')">'+ (result.data['status'] === "1" ? 'Active' : 'Non-Active') +'</button>',
            addActionOption(result.data['id'],result.data['name'], result.data['phone'], result.data['status'], parseInt(rows)+1)
          ]).draw().node().id = "rows_"+result.data['id'];
        } else {
          // Update
          const newData = [
            idEdit,
            result.data['name'],
            result.data['phone'],
            `${result.address['regency_name']}, ${result.address['province_name']},`,
              '<button type="button" class="btn btn-'+ (result.data['status'] === "1" ? 'success' : 'danger')+' btn-sm" onclick="changeStatus('+result.data['id']+', '+idEdit+')">'+ (result.data['status'] === "1" ? 'Active' : 'Non-Active') +'</button>',
            addActionOption(result.data['id'], result.data['name'], result.data['phone'], result.data['status'], idEdit)
          ];
          subscribeTable.row($("#rows_"+$("#id").val())).data(newData);
        }

        if($('#_method').val() === "POST") {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Data Subscriber has been Created!',
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Data Subscriber has been Updated!',
            showConfirmButton: false,
            timer: 1500
          });
        }
        
        resetForm();
      }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        Swal.fire({
          icon: 'error',
          title: 'Oops... ' + textStatus,
          text: 'Please Try Again or Refresh Page!'
        });
      });
    }
  });

  function resetForm() {   
    $('#addSubscribeForm').trigger('reset');
    $("input").removeClass('is-invalid');
    $(".invalid-feedback").remove();
    $('#status, #regencies').val("").change();
    $('#regencies').empty();
    $('#regencies').append('<option value="" disabled selected>--Select Regency--</option>');
    $('#regencies').prop('disabled', true);
    $("#id").val('');
    $("#idEdit").val('');
    $("#_method").val('POST');
    

    $("#form_title").text('Add Subscriber');
    $(".btn-add-subscriber").text("Submit");
  }

  function searchProvince() {
    $('#regencies').empty();
    $('#regencies').append('<option value="" disabled selected>--Select Regency--</option>');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.regencies.index') }}?provinces=" + $('#provinces').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#regencies').append(`
              <option value="${value['id']}">${value['name']}
              </option>`);
          });
          $('#regencies').prop('disabled', false);
        } else {
          console.log("data trouble");
        }
      }
    })
  }

  function sendPersonalMessage() {
    if($('#personal_body_message').val()) {
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('admin.subscribers.personal') }}",
        type : "POST",
        dataType : "json",
        data: {
          phoneTo : $('#personal_phone_to').val(),
          body    : $('#personal_body_message').val()
        },
        error: function (xhr, status, error) {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!'
          });
        },
        success:function(result) {
          if(result) {
            Swal.fire({
              position: 'middle',
              icon: 'success',
              title: 'Your Message to '+ $('#personal_phone_to').val() +' has been sent',
              showConfirmButton: false,
              timer: 1500
            });
          } else {
            console.log("data trouble");
          }
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Check your body message again!'
      });
    }
  }

  function changeSenders(input) {
    if(input.value === 'regency') {
      $('#section-from').after(`
        <div class="col-lg-12" id="section-from-regency">
          <div class="form-group">
            <div class="input-group input-group-merge">
              <select name="multiple_phone_regency" id="multiple_phone_regency" class="form-control" data-toggle="select">
                <option value="" disabled selected>--Select Regency Senders--</option>
              </select>
            </div>
          </div>
        </div>
      `);
      $('#multiple_phone_regency').select2();
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('admin.subscribers.getRegency') }}",
        type : "GET",
        dataType : "json",
        error: (xhr, status, error) => console.log(xhr, status),
        success: function (result) {
          $.each(result.data, (key, value) => {
            $('#multiple_phone_regency').append(`
              <option value="${value['id']}">${value['name']} - ${value['province']}</option>
            `);
          })
        }
      });
    } else {
      $('#section-from-regency').remove();
    }
  }

  function sendMultipleMessage() {
    if($('#multiple_body_message').val()) {
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('admin.subscribers.multiple') }}",
        type : "POST",
        dataType : "json",
        data: {
          type       : $('#multiple_phone').val(),
          regency_id : $('#multiple_phone_regency').val(),
          body       : $('#multiple_body_message').val()
        },
        error: function (xhr, status, error) {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!'
          });
        },
        success:function(result) {
          if(result) {
            Swal.fire({
              position: 'middle',
              icon: 'success',
              title: 'Your Message to '+ $('#multiple_phone_to').text() +' has been sent',
              showConfirmButton: false,
              timer: 1500
            });
            $('#section-from-regency').remove();
          } else {
            console.log("data trouble");
          }
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Check your body message again!'
      });
    }
  }
</script>
    
@endsection