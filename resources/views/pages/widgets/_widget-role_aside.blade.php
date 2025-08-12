<!--begin::Aside-->
<div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px" id="kt_profile_aside">
  <!--begin::Profile Card-->
  <div class="card card-custom card-stretch">
    <!--begin::Body-->
    <div class="card-body pt-4">
      <!--begin::User-->
      <div class="d-flex align-items-center">
        <div>
          <a href="#" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ __('Access Management Name') }}: <b>{{ $role->name }}</b></a>
        </div>
      </div>
      <!--end::User-->
      <!--begin::Contact-->
      <div class="py-9 d-flex flex-column flex-lg-fill float-left mb-7">
      	<span class="font-weight-bolder mb-4">{{ __('Users assigned with this access management') }}:</span>
      	<div class="symbol-group symbol-hover">
          @foreach ($role->users->take(5) as $user)
            <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="{{ $user->name }}">
              <img alt="Pic" src="{{ asset($user->avatar_path) }}" />
            </div>
          @endforeach
          @if ( $role->users->count() > 5 )
            <div class="symbol symbol-30 symbol-circle symbol-light">
              <span class="symbol-label font-weight-bold">{{ $role->users->count() - 5 }}+</span>
            </div>
          @endif
      	</div>
      </div>
      <!--end::Contact-->
    </div>
    <!--end::Body-->
  </div>
  <!--end::Profile Card-->
</div>
<!--end::Aside-->
