{{-- Subheader V1 --}}

<div class="subheader py-2 {{ Metronic::printClasses('subheader', false) }}" id="kt_subheader">
    <div class="{{ Metronic::printClasses('subheader-container', false) }} d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">

		{{-- Info --}}
        <div class="d-flex align-items-center flex-wrap mr-1 font-size-h5">

			{{-- Page Title --}}
            <h5 class="text-dark font-weight-bold my-2 mr-5">
                @if (config('layout.mobile_aside.self.display'))
                  <!--begin::Mobile Toggle-->
                  <button class="burger-icon burger-icon-left mr-4 d-inline-block d-lg-none" class="kt_subheader_mobile_toggle" id="kt_subheader_mobile_toggle">
                    <span></span>
                  </button>
                  <!--end::Mobile Toggle-->
                @endif

                <span class="float-right max-w-180px max-w-md-100">
                  {{ @$page_title }}
                  @if (isset($page_description) && config('layout.subheader.displayDesc'))
                      <small class="d-block">{{ @$page_description }}</small>
                  @endif
                </span>
            </h5>

            @if (!empty($page_breadcrumbs))
				{{-- Separator --}}
                <div class="subheader-separator subheader-separator-ver my-2 mr-4 d-none"></div>

				{{-- Breadcrumb --}}
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2">
                    <li class="breadcrumb-item"><a href="#"><i class="flaticon2-shelter text-muted icon-1x"></i></a></li>
                    @foreach ($page_breadcrumbs as $k => $item)
						<li class="breadcrumb-item">
                        	<a href="{{ url($item['page']) }}" class="text-muted">
                            	{{ $item['title'] }}
                        	</a>
						</li>
                    @endforeach
                </ul>
            @endif
        </div>

		{{-- Toolbar --}}
        <div class="d-flex align-items-center subheader-part-right">

            @hasSection('page_toolbar')
                @section('page_toolbar')
            @endif

            @isset($lohn)
              @if ($lohn->konfirm == 1)
                <div class="alert alert-custom alert-outline-success fade show mb-0 py-0" role="alert">
                    <div class="alert-icon"><i class="flaticon-interface-10"></i></div>
                    <div class="alert-text">Lohn Confirmed: <div class="text-muted">{{ $lohn->timestamp }}</div></div>
                </div>
              @endif
            @endisset

            @isset($subheader_buttons)
              @foreach ($subheader_buttons as $key => $button)
                <a href="{{ $button->url }}" class="btn btn-light-{{ isset($button->color) ? $button->color : 'primary' }} font-weight-bold ml-2">{!! $button->text !!}</a>
              @endforeach
            @endisset
            @isset($subheader_button_forms)
              @foreach ($subheader_button_forms as $key => $form)
                {{-- <a href="{{ $button->url }}" class="btn btn-light-primary font-weight-bold ml-2">{{ $button->text }}</a> --}}
                <form method="{{ $form->method }}" action="{{ $form->action }}">
                    {{ csrf_field() }}
                    {{ method_field($form->method_field) }}
                    <input
                    type="submit"
                    class="btn btn-light-{{ isset($form->color) ? $form->color : 'primary' }} font-weight-bold ml-2 px-2"
                    @isset($form->confirm)
                      onclick="return confirm('{{ $form->confirm }}')"
                    @endisset
                    value="{{ $form->text }}"
                    >
                </form>
              @endforeach
            @endisset



        </div>

    </div>
</div>
