<!--begin::Aside-->
<div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px" id="kt_profile_aside">
  <!--begin::Profile Card-->
  <div class="card card-custom card-stretch">
    <!--begin::Body-->
    <div class="card-body pt-4">
      <!--begin::Device-->
      <div class="d-flex align-items-center">
        <!--begin: Title-->
        <h2 class="d-flex align-items-center text-dark text-hover-primary font-size-h2 font-weight-bold mr-3">{{ $device->name }}
          <i class="flaticon2-correct text-success icon-md ml-2"></i>
        </h2>
        <span class="text-muted font-weight-bold">ID: {{ $device->id }}</span>
        <!--end::Title-->
      </div>
      <!--end::Device-->
      <!--begin::Nav-->
      <div class="navi navi-bold navi-hover navi-active navi-link-rounded py-9">
        <div class="navi-item mb-2">
          <a href="{{ route('devices.show', $device->id) }}" class="navi-link py-4 {{ $item_active == 'view' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:media/svg/icons/Devices/Tablet.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"></rect>
                      <path d="M6.5,4 L6.5,20 L17.5,20 L17.5,4 L6.5,4 Z M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z" fill="#000000" fill-rule="nonzero"></path>
                      <polygon fill="#000000" opacity="0.3" points="6.5 4 6.5 20 17.5 20 17.5 4"></polygon>
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Details') }}</span>
          </a>
        </div>
        <div class="navi-item mb-2">
          <a href="{{ route('devices.auth', $device->id) }}" class="navi-link py-4 {{ $item_active == 'auth' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:media/svg/icons/Home/Key.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"></rect>
                      <polygon fill="#000000" opacity="0.3" transform="translate(8.885842, 16.114158) rotate(-315.000000) translate(-8.885842, -16.114158) " points="6.89784488 10.6187476 6.76452164 19.4882481 8.88584198 21.6095684 11.0071623 19.4882481 9.59294876 18.0740345 10.9659914 16.7009919 9.55177787 15.2867783 11.0071623 13.8313939 10.8837471 10.6187476"></polygon>
                      <path d="M15.9852814,14.9852814 C12.6715729,14.9852814 9.98528137,12.2989899 9.98528137,8.98528137 C9.98528137,5.67157288 12.6715729,2.98528137 15.9852814,2.98528137 C19.2989899,2.98528137 21.9852814,5.67157288 21.9852814,8.98528137 C21.9852814,12.2989899 19.2989899,14.9852814 15.9852814,14.9852814 Z M16.1776695,9.07106781 C17.0060967,9.07106781 17.6776695,8.39949494 17.6776695,7.57106781 C17.6776695,6.74264069 17.0060967,6.07106781 16.1776695,6.07106781 C15.3492424,6.07106781 14.6776695,6.74264069 14.6776695,7.57106781 C14.6776695,8.39949494 15.3492424,9.07106781 16.1776695,9.07106781 Z" fill="#000000" transform="translate(15.985281, 8.985281) rotate(-315.000000) translate(-15.985281, -8.985281) "></path>
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Authentication') }}</span>
          </a>
        </div>
        <div class="navi-item mb-2">
          <a href="{{ route('devices.report', $device->id) }}" class="navi-link py-4 {{ $item_active == 'report' ? 'active' : '' }}">
            <span class="navi-icon mr-2">
              <span class="svg-icon">
                <!--begin::Svg Icon | path:media/svg/icons/General/Clipboard.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"></rect>
                      <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"></path>
                      <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"></path>
                      <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"></rect>
                      <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"></rect>
                  </g>
                </svg>
                <!--end::Svg Icon-->
              </span>
            </span>
            <span class="navi-text font-size-lg">{{ __('Report') }}</span>
          </a>
        </div>
      </div>
      <!--end::Nav-->
    </div>
    <!--end::Body-->
  </div>
  <!--end::Profile Card-->
</div>
<!--end::Aside-->
