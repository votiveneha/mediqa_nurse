@extends('nurse.layouts.layout')
@section('content')
<main class="main">
  <div class="bg-homepage4"></div>
  <section class="section-box mb-70">
    <div class="banner-hero hero-1 banner-homepage5">
      <div class="banner-inner">
        <div class="row align-items-center">
          <div class="col-xl-7 col-lg-12">
            
            <div class="block-banner pt-0">
            <div class="box-search-2 mb-40">
          <div class="block-banner p-0">
            <div class="form-find  wow animate__animated animate__fadeIn w-100" data-wow-delay=".2s">
              <form class="search-form">
                <div class="w-100 ">
                <label for="" class="form-label">What</label>
                <input class="form-input input-keysearch mr-10 py-0" style="height: unset;" type="text" placeholder="Job Title, Keywords, Specialty">
                </div>
                <div class="form-find-select mr-10">
                <label for="" class="form-label">Where</label>
                <select class="form-input mr-10 select-active" style="height: unset;">
                  <option value="">Job Location</option>
                  <option value="AX">option 1</option>
                  <option value="AF">option 2</option>

                </select>
                </div>
                <a class="btn btn-default btn-find font-sm" href='{{ route("nurse.login") }}'></a>
              </form>
            </div>
          </div>
        </div>
              <!-- <h1 class="heading-banner wow animate__animated animate__fadeInUp"> Register and<br class="d-none d-lg-block">create your profile</h1> -->
              <div class="banner-description mt-20 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                Refine your search by specialty, location, time, and assignment duration. Apply directly for the ideal shift or permanent position. Connect with Medical facilities and agencies.</div>
              <div class="mt-30"> <a class="btn btn-default mr-15" href="{{ route('nurse.nurse-register') }}">Register in 1 Minute</a>
              </div>

            </div>
          </div>
          <div class="col-xl-5 col-lg-12 d-none d-xl-block col-md-6">
            <div class="banner-imgs">
              <div class="banner-1 shape-1"><img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse1.png')}}"></div>
              <div class="banner-2 shape-2"><img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse2.png')}}"></div>
              <div class="banner-3 shape-3"><img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse3.png')}}"></div>
              <div class="banner-4 shape-3"><img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse4.png')}}"></div>
              <div class="banner-5 shape-2"><img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse5.png')}}"></div>
              <div class="banner-6 shape-1"><img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse6.png')}}"></div>
            </div>
          </div>
        </div>
      
      </div>
    </div>
  </section>



  <section class="section-box mt-70 mb-40">
    <div class="container">
      <div class="text-center">
        <h2 class="section-title mb-10 wow animate__animated animate__fadeInUp">You're crucial!</h2>
        {{-- <p class="font-lg color-text-paragraph-2 wow animate__animated animate__fadeInUp">Just via some simple steps, you will find your ideal candidates you’r looking for!" : You're crucial!</p> --}}
        <p class="font-lg color-text-paragraph-2 wow animate__animated animate__fadeInUp">With just a few simple steps, you'll find the ideal nursing and midwifery jobs you are looking for!</p>

      </div>
      <div class="mt-70">
        <div class="row">
          <div class="col-lg-4">
            <div class="box-step step-1">
              <h1 class="number-element">1</h1>
              <h4 class="mb-10">You have the <br>power</h4>
              <p class="font-lg color-text-paragraph-2">Customize your profile with personal <br> information, certifications, preferences, specified qualifications, credentials,<br> experience levels, references and any other relevant details.
              </p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="box-step step-2">
              <h1 class="number-element">2</h1>
              <h4 class="mb-10">We stay in <br>touch</h4>
              <p class="font-lg color-text-paragraph-2">Behind our software using latest <br> Ai technology, we are here to understand <br> your needs and help you to go <br>through this fast process. </p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="box-step">
              <h1 class="number-element">3</h1>
              <h4 class="mb-10">Receive tailored <br>job offers fast</h4>
              <p class="font-lg color-text-paragraph-2">Medical facilities and agencies <br> are actively competing, prompting <br> them to move quickly</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-box mt-50">
    <div class="section-box wow animate__animated animate__fadeIn">
      <div class="container">

        <div class="text-center">
          <h2 class="section-title mb-10 wow animate__animated animate__fadeInUp">Latest Jobs</h2>
        </div>



        <div class="row mt-50">
          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
            <div class="card-grid-2 grid-bd-16 hover-up">
              <div class="card-grid-2-image"><span class="lbl-hot bg-green"><span>Anaesthetics</span></span>
                <div class="image-box">
                  <figure><img src="{{ asset('nurse/assets/imgs/page/homepage2/img1.png')}}" alt="jobBox"></figure>
                </div>
              </div>
              <div class="card-block-info">
                <h5><a href='#'>Anaesthetics</a></h5>
                <div class="mt-5"><span class="card-location mr-15">New York, US</span><span class="card-time">7:00 AM - 5:30 PM</span></div>
                <div class="card-2-bottom mt-20">
                  <div class="row">
                    <div class="col-xl-7 col-md-7 mb-2">
                      <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>

                    </div>
                    <div class="col-xl-5 col-md-5 text-lg-end"><span class="card-text-price">$90 - $120</span><span class="text-muted">/Hour</span></div>
                  </div>
                </div>
                <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
            <div class="card-grid-2 grid-bd-16 hover-up">
              <div class="card-grid-2-image"><span class="lbl-hot"><span>Full time</span></span>
                <div class="image-box">
                  <figure><img src="{{ asset('nurse/assets/imgs/page/homepage2/img2.png')}}" alt="jobBox"></figure>
                </div>
              </div>
              <div class="card-block-info">
                <h5><a href='#'>Gen Med/Gen Surg Ward</a></h5>
                <div class="mt-5"><span class="card-location mr-15">New York, US</span><span class="card-time">7:00 AM - 5:30 PM</span></div>
                <div class="card-2-bottom mt-20">
                  <div class="row">
                    <div class="col-xl-7 col-md-7 mb-2">
                      <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>

                    </div>
                    <div class="col-xl-5 col-md-5 text-lg-end"><span class="card-text-price">$80 - $150</span><span class="text-muted">/Hour</span></div>
                  </div>
                </div>
                <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
            <div class="card-grid-2 grid-bd-16 hover-up">
              <div class="card-grid-2-image"><span class="lbl-hot"><span>Full time</span></span>
                <div class="image-box">
                  <figure><img src="{{ asset('nurse/assets/imgs/page/homepage2/img3.png')}}" alt="jobBox"></figure>
                </div>
              </div>
              <div class="card-block-info">
                <h5><a href='#'>Anaesthetics</a></h5>
                <div class="mt-5"><span class="card-location mr-15">New York, US</span><span class="card-time">7:00 AM - 5:30 PM</span></div>
                <div class="card-2-bottom mt-20">
                  <div class="row">
                    <div class="col-xl-7 col-md-7 mb-2">
                      <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>

                    </div>
                    <div class="col-xl-5 col-md-5 text-lg-end"><span class="card-text-price">$120 - $150</span><span class="text-muted">/Hour</span></div>
                  </div>
                </div>
                <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
            <div class="card-grid-2 grid-bd-16 hover-up">
              <div class="card-grid-2-image"><span class="lbl-hot"><span>Full time</span></span>
                <div class="image-box">
                  <figure><img src="{{ asset('nurse/assets/imgs/page/homepage2/img4.png')}}" alt="jobBox"></figure>
                </div>
              </div>
              <div class="card-block-info">
                <h5><a href='#'>Gen Med/Gen Surg Ward</a></h5>
                <div class="mt-5"><span class="card-location mr-15">New York, US</span><span class="card-time">7:00 AM - 5:30 PM</span></div>
                <div class="card-2-bottom mt-20">
                  <div class="row">
                    <div class="col-xl-7 col-md-7 mb-2">
                      <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>

                    </div>
                    <div class="col-xl-5 col-md-5 text-lg-end"><span class="card-text-price">$80 - $150</span><span class="text-muted">/Hour</span></div>
                  </div>
                </div>
                <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
            <div class="card-grid-2 grid-bd-16 hover-up">
              <div class="card-grid-2-image"><span class="lbl-hot"><span>Full time</span></span>
                <div class="image-box">
                  <figure><img src="{{ asset('nurse/assets/imgs/page/homepage2/img5.png')}}" alt="jobBox"></figure>
                </div>
              </div>
              <div class="card-block-info">
                <h5><a href='#'>Anaesthetics</a></h5>
                <div class="mt-5"><span class="card-location mr-15">New York, US</span><span class="card-time">7:00 AM - 5:30 PM</span></div>
                <div class="card-2-bottom mt-20">
                  <div class="row">
                    <div class="col-xl-7 col-md-7 mb-2">
                      <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>

                    </div>
                    <div class="col-xl-5 col-md-5 text-lg-end"><span class="card-text-price">$80 - $150</span><span class="text-muted">/Hour</span></div>
                  </div>
                </div>
                <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
            <div class="card-grid-2 grid-bd-16 hover-up">
              <div class="card-grid-2-image"><span class="lbl-hot"><span>Full time</span></span>
                <div class="image-box">
                  <figure><img src="{{ asset('nurse/assets/imgs/page/homepage2/img6.png')}}" alt="jobBox"></figure>
                </div>
              </div>
              <div class="card-block-info">
                <h5><a href='#'>Gen Med/Gen Surg Ward</a></h5>
                <div class="mt-5"><span class="card-location mr-15">New York, US</span><span class="card-time">7:00 AM - 5:30 PM</span></div>
                <div class="card-2-bottom mt-20">
                  <div class="row">
                    <div class="col-xl-7 col-md-7 mb-2">
                      <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>

                    </div>
                    <div class="col-xl-5 col-md-5 text-lg-end"><span class="card-text-price">$80 - $150</span><span class="text-muted">/Hour</span></div>
                  </div>
                </div>
                <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
              </div>
            </div>
          </div>
        </div>




      </div>
    </div>
  </section>
  <!-- <section class="section-box overflow-visible mt-50 mb-0 bg-cat2">
    <div class="container">
      <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="text-center">
            <h1 class="color-brand-2"><span class="count">25</span><span> K+</span></h1>
            <h5>Completed Cases</h5>
            <p class="font-sm color-text-paragraph mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p>
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="text-center">
            <h1 class="color-brand-2"><span class="count">17</span><span> +</span></h1>
            <h5>Our Office</h5>
            <p class="font-sm color-text-paragraph mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p>
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="text-center">
            <h1 class="color-brand-2"><span class="count">86</span><span> +</span></h1>
            <h5>Skilled People</h5>
            <p class="font-sm color-text-paragraph mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p>
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="text-center">
            <h1 class="color-brand-2"><span class="count">28</span><span> +</span></h1>
            <h5>Happy Clients</h5>
            <p class="font-sm color-text-paragraph mt-10">We always provide people a <br class="d-none d-lg-block">complete solution upon focused of <br class="d-none d-lg-block">any business</p>
          </div>
        </div>
      </div>
    </div>
  </section> -->

  <section class="section-box overflow-visible mt-80 mb-100">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-sm-12">
          <div class="box-image-job">
            <!-- <img class="img-job-1" alt="jobBox" src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png')}}"> -->
            {{-- <img class="img-job-2" alt="jobBox" src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png')}}"> --}}
            <figure class="wow animate__ animate__fadeIn animated" style="visibility: visible; animation-name: fadeIn;"><img alt="jobBox" src="{{ asset('nurse/assets/imgs/img1.png')}}"></figure>
          </div>
        </div>
        <div class="col-lg-6 col-sm-12">
          <div class="content-job-inner">
            <h2 class="text-52 wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">You have the power, receive tailored job offers fast!</h2>
            <div class="mt-20 pr-50 text-md-lh28 wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">Accept or reject interview invitations. Evaluate multiple offers from leading hospitals. Select the best match for your needs.</div>
            <div class="mt-20">
              <div class="wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;"><a class="btn btn-default" href='{{ route("nurse.login") }}'>Get Interviews</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>














  <section class="section-box bg-15 pt-50 pb-50 mt-80">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 text-center"><img class="img-job-search mt-20" src="{{ asset('nurse/assets/imgs/page/homepage3/img-job-search.png')}}" alt="jobBox"></div>
        <div class="col-xl-5 col-lg-6 col-md-12 col-sm-12">
          <h2 class="mb-40 text-white">Job search for people passionate about startup</h2>
          <div class="box-checkbox mb-30">
            <h6 class="text-white">Create an account</h6>
            <p class="text-white font-sm opacity_6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p>
          </div>
          <div class="box-checkbox mb-30">
            <h6 class="text-white">Search for Jobs</h6>
            <p class="text-white font-sm opacity_6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p>
          </div>
          <div class="box-checkbox mb-30">
            <h6 class="text-white">Save &amp; Apply</h6>
            <p class="text-white font-sm opacity_6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p>
          </div>
        </div>
      </div>
    </div>
  </section>









  <script src="{{ asset('nurse/assets/js/plugins/counterup.js')}}"></script>
</main>

@endsection
@section('js')

@endsection