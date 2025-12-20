    <div class="modal fade text-left" id="AddBuyer" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addBuyerForm" action="{{route('admin.buyersAction','create')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Add Buyer</h4>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Buyer Name *</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Buyer Name" required>
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control" name="company_name" placeholder="Enter Company Name">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                        </div>
                        <div class="form-group">
                            <label>Mobile</label>
                            {{-- <input type="text" class="form-control" name="mobile" placeholder="Enter Mobile"> --}}
                            <input type="tel" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" minlength="11" maxlength="11" pattern="[0-9]{11}" title="Please enter exactly 11 digits" oninput="this.value = this.value.slice(0, 11);" placeholder="Please enter exactly 11 digits with start 0" required>

                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <select name="country" class="form-control">
                                <option value="">-- Select Country --</option>
                                @foreach (geoData(1) as $c)
                                    <option value="{{ $c->name }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address Line</label>
                            <input type="text" class="form-control" name="address" placeholder="Enter Address">
                            <input type="hidden" class="form-control" name="api" value="1" placeholder="Enter Address">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Buyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
