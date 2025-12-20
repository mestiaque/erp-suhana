<div class="modal fade text-left" id="AddMerchandisers" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addMerchandiserForm" action="{{route('admin.merchandisersAction','create')}}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add Merchandisers</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times; </span>
                    </button>
                </div>

                <div class="modal-body">
                        <div class="form-group">
                        <label for="name">Name* </label>
                        <div class="controls">
                            <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
                            @if ($errors->has('name'))
                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                            @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Mobile* </label>
                            <div class="controls">
                                <input type="tel" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" minlength="11" maxlength="11" pattern="[0-9]{11}" title="Please enter exactly 11 digits" oninput="this.value = this.value.slice(0, 11);" placeholder="Please enter exactly 11 digits with start 0" required>
                                @if ($errors->has('mobile'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Email </label>
                            <div class="controls">
                                <input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email">
                                @if ($errors->has('email'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" class="form-control" name="api" value="1" placeholder="">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Merchandisers</button>
                </div>
            </form>
        </div>
    </div>
</div>
