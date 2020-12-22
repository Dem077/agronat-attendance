<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h2>Change Password</h2>
            </div>

            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
                <form>
                    @csrf
                    <div class="form-group">
                        <label for="current_password" >{{ __('Current Password') }}</label>
                        <input id="current_password" type="password" class="form-control" wire:model="current_password" name="current_password" autocomplete="current-password" />
                        @error('current_password') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password" >{{ __('New Password') }}</label>
                        <input id="password" type="password" class="form-control" wire:model="password" name="password" autocomplete="new-password" />
                        @error('password') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation" >{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" type="password" class="form-control" wire:model="password_confirmation" name="password_confirmation" autocomplete="new-password" />
                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <button wire:click.prevent="update()" class="btn btn-dark" >Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
