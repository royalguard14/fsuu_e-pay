@extends('layouts.master')
@section('header')
Profile Management
@endsection
@section('content')
<!-- Check for Session Success -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toast.fire({
            icon: '{{ session('icon') }}',
            title: '{{ session('success') }}'
        });
    });
</script>
@endif
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Toast.fire({
            icon: 'error',
            title: '{{ $errors->first() }}'  // Show the first validation error
        });
    });
</script>
@endif
<div class="row">
  <div class="col-md-3">
    <!-- Profile Image -->
    <div class="card card-primary card-outline">
      <div class="card-body box-profile">
        <div class="text-center">


         <form action="{{ route('profiles.updatePicture', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <img 
            id="profileImage" 
            class="profile-user-img img-fluid img-circle"
            src="{{ $profile->profile_picture && file_exists(storage_path('app/public/' . $profile->profile_picture)) ? asset('storage/' . $profile->profile_picture) : asset('dist/img/user2-160x160.jpg') }}" 
            alt="User profile picture">
        </div>
        <h3 class="profile-username text-center">{{ $profile->full_name }}</h3>
        <p class="text-muted text-center">{{ auth()->user()->role->role_name }}</p>
        <div class="text-center">
        <label for="profile_picture" class="btn btn-outline-primary ">
            <i class="fas fa-upload"></i> Upload New
        </label>
        <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*">
        </div>
        <button type="submit" class="btn btn-success btn-block">Change Profile Picture</button>
    </form>
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->

</div>
<!-- /.col -->
<div class="col-md-9">
    <div class="card">
      <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#information" data-toggle="tab">Information</a></li>
            <li class="nav-item"><a class="nav-link" href="#account" data-toggle="tab">Account</a></li>
            <li class="nav-item"><a class="nav-link" href="#activities" data-toggle="tab">Activities</a></li>
        </ul>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="tab-content">
          <div class="active tab-pane" id="information">
                             <!-- Profile Information Form -->
                <form action="{{ route('profiles.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- First Name and Last Name -->
                    <div class="row">
                        <!-- First Name -->
                        <div class="form-group col-md-6">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" name="firstname" value="{{ $profile->firstname }}" class="form-control" required>
                        </div>

                        <!-- Last Name -->
                        <div class="form-group col-md-6">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" name="lastname" value="{{ $profile->lastname }}" class="form-control" required>
                        </div>
                    </div>

                    <!-- Phone and Birthdate -->
                    <div class="row">
                        <!-- Phone -->
                        <div class="form-group col-md-6">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" id="phone_number" name="phone_number" value="{{ $profile->phone_number }}" class="form-control">
                        </div>

                        <!-- Birthdate -->
                        <div class="form-group col-md-6">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" id="birthdate" name="birthdate" value="{{ $profile->birthdate }}" class="form-control">
                        </div>
                    </div>

                    <!-- Gender and Nationality -->
                    <div class="row">
                        <!-- Gender -->
                        <div class="form-group col-md-6">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="Male" {{ $profile->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $profile->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ $profile->gender == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Nationality -->
                        <div class="form-group col-md-6">
                            <label for="nationality">Nationality</label>
                            <input type="text" id="nationality" name="nationality" value="{{ $profile->nationality }}" class="form-control">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="2">{{ $profile->address }}</textarea>
                    </div>

                    <!-- Bio -->
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="3">{{ $profile->bio }}</textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                    </div>

                </form>
         </div>
<div class="tab-pane" id="account">
    <!-- Profile Information Form -->
    <form action="{{ route('profiles.updateAccount', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Username -->
        <div class="form-group mt-4">
            <label for="username">New Username</label>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" class="form-control" required>
        </div>

        <!-- Current Password (Optional) -->
        <div class="form-group mt-4">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="form-control">
        </div>

        <!-- New Password (Optional) -->
        <div class="form-group mt-4">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control">
        </div>

        <!-- Confirm New Password (Optional) -->
        <div class="form-group mt-4">
            <label for="new_password_confirmation">Confirm New Password</label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control">
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-5">Save Changes</button>
        </div>
    </form>
</div>


        <div class="tab-pane" id="activities">

      <table class="table table-head-fixed text-nowrap" id="example3">
        <thead>
            <tr>
                <th>No.</th>
                <th>Description</th>
                <th>Action Date</th>
            
            </tr>
        </thead>
        <tbody>
            @foreach($activityLogs as $log)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->created_at->diffForHumans() }}</td>
                   
                </tr>
            @endforeach
        </tbody>
    </table>















        </div>
<!-- /.tab-pane -->
</div>
<!-- /.tab-content -->
</div><!-- /.card-body -->
</div>
<!-- /.card -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const profilePictureInput = document.getElementById('profile_picture');
        if (profilePictureInput) {
            profilePictureInput.onchange = function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                    // Find the image element and update the source
                        const imgElement = document.getElementById('profileImage');
                        if (imgElement) {
                            imgElement.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            };
        }
    });
</script>
@endsection