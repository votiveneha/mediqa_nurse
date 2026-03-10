@extends('nurse.layouts.layout') @section('content')
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f9fc;
        }

        /* HERO */
        .hero {
            background: #f2f4f7;
        }

        .hero-title {
            font-size: 38px;
            font-weight: 700;
        }

        .hero-text {
            color: #666;
            font-size: 16px;
            font-weight: 500
        }

        .create-btn {
            /* background: #fff3cd; */
            background: #50b5a3;
            color: #fff;
            padding: 12px 20px;
            display: inline-block;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
        }

        .hero-img {
            width: 90%;
            border-radius: 15px;
        }

        .new-hero-section .block-banner {
            background: transparent !important;
            padding: 20px !important;
        }

        .new-hero-section .block-banner .form-find {
            background: transparent !important;
            box-shadow: none;
        }

        .bg-white {
            background: white;
        }

        .shadow-white {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        /* FILTER */
        .filters {
            border-radius: 10px;
            /* display: grid;
                    grid-template-columns: repeat(5, 1fr); */
        }

        .filter-item {
            /* padding: 10px 0; */
            font-weight: 600;
            font-size: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .filter-item h4 {
            font-size: 14px;
            text-align: left;
        }

        .filter-item p {
            font-size: 12px;
            color: #888;
            margin: 0;
            text-align: left;
        }

        /* FEATURE CARDS */
        .feature-card {
            background: #edf0f5;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            height: 100%;
        }

        .feature-card h5 {
            font-size: 16px;
        }

        .nurse-card {
            display: flex;
            flex-direction: column;
        }

        .feature-card h4 {
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 20px;
            text-align: center;
            margin-top: 15px;
        }

        .feature-card ul {
            margin-top: 6px;
            list-style: none;
            padding-left: 0;
            display: flex;
            flex-direction: column;
        }

        .feature-card li {
            position: relative;
            padding-left: 22px;
            margin-bottom: 6px;
            color: #666;
            display: inline-block;
            text-align: left;
            font-weight: 500;
        }

        .feature-card li::after {
            content: "\f00c";
            font-family: "FontAwesome";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #50b5a3;
        }

        .feature-card a {
            color: #2d2de3d4;
            cursor: pointer;
            padding: 0 20px;
        }

        .mt-15 {
            margin-top: 30px !important;
        }

        /* JOB CARDS */
        .job-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .job-content {
            padding: 20px;
        }

        .job-price {
            font-weight: 600;
            color: #000;
            margin-top: 10px;
            background: #2d6cdf21;
            width: fit-content;
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 14px;
        }

        .job-right-content {
            font-size: 14px;
            font-weight: 600;
            color: #000;

        }

        .job-content button {
            margin-top: 20px;
            width: 100%;
            background: #edf0f5;
        }

        .nurse-card i {
            color: #50b5a3;
            margin-right: 5px;
            font-size: 14px;
            text-align: center;
        }

        .hr-bg {
            background: #6666668c;
            margin: 12px 0;
        }

        .star {
            color: #ddbc52;
            font-size: 18px;
        }

        .circle-check i {
            color: #50b5a3;
        }

        .filter-item i {
            font-size: 22px;
        }

        /* 10/03  */
        .filters {
            border-radius: 14px;
            overflow: hidden;
        }

        .filters .col-md-2 {
            border-right: 1px solid #eceff4;
            transition: all .3s ease;
        }

        .filters .col-md-2:last-child {
            border-right: none;
        }

        .filter-item {
            transition: all .25s ease;
        }

        .filter-item:hover {
            transform: translateY(-3px);
        }

        /* icon circle */

        .filter-item i {
            font-size: 18px;
        }

        .circle-check i,
        .clock i {
            background: #eef7f5;
            color: #50b5a3;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        /* star */

        .star {
            background: #fff5da;
            color: #d7b64b;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        /* headings */

        .filter-item h4 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }

        .filter-item p {
            font-size: 12px;
            color: #8a8f98;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        /* modal  */
        .right-modal .modal-dialog {
            position: fixed;
            right: 0;
            top: 0;
            margin: 0;
            height: 100%;
            width: 420px;
            transform: translateX(100%);
            transition: transform .3s ease;
        }

        .right-modal.show .modal-dialog {
            transform: translateX(0);
        }

        .right-modal .modal-content {
            height: 100%;
            border-radius: 0;
        }

        .right-modal .modal-body {
            overflow-y: auto;
        }

        .text-30 {
            font-size: 28px;
            font-weight: 700;
        }

        .credential i {
            color: #50b5a3;
            margin-right: 6px;
        }

        .credential h4 {
            font-size: 16px;
            margin-top: 12px;
        }

        .latest-job-card {
            position: relative;
        }

        .latest-job-card .top-note {
            position: absolute;
            top: 6px;
            left: 9px;
        }

        .top-note p {
            background: #50b5a3;
            color: #fff;
            padding: 0px 10px;
            font-size: 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .stop h4{
          font-size: 16px;
        }
        .stop h4 span{
          font-size: 20px;
          font-weight: 800;
          margin-left: 4px;
        }
    </style>
    <main class="main">
        <div class="bg-homepage4"></div>
        {{-- <section class="section-box mb-70">
		<div class="banner-hero hero-1 banner-homepage5">
			<div class="banner-inner">
				<div class="row align-items-center">
					<div class="col-xl-7 col-lg-12">
						<div class="block-banner pt-0">
							<div class="box-search-2 mb-40">
								<div class="block-banner p-0">
									<div class="form-find  wow animate__animated animate__fadeIn w-100"
                                            data-wow-delay=".2s">
										<form class="search-form">
											<div class="w-100 ">
												<label for="" class="form-label">What</label>
												<input class="form-input input-keysearch mr-10 py-0"
                                                        style="height: unset;" type="text"
                                                        placeholder="Job Title, Keywords, Specialty">
												</div>
												<div class="form-find-select mr-10">
													<label for="" class="form-label">Where</label>
													<select class="form-input mr-10 select-active" style="height: unset;">
														<option value="">Job Location</option>
														<option value="AX">option 1</option>
														<option value="AF">option 2</option>
													</select>
												</div>
												<a class="btn btn-default btn-find font-sm"
                                                    href='{{ route('nurse.login') }}'></a>
  </form>
  </div>
  </div>
  </div>
  <!-- <h1 class="heading-banner wow animate__animated animate__fadeInUp"> Register and<br class="d-none d-lg-block">create your profile</h1> -->
  <div class="banner-description mt-20 wow animate__animated animate__fadeInUp" data-wow-delay=".1s"> Refine your search by specialty, location, time, and assignment duration. Apply directly for the ideal shift or permanent position. Connect with Medical facilities and agencies. </div>
  <div class="mt-30">
    <a class="btn btn-default mr-15" href="{{ route('nurse.nurse-register') }}">Register in 1 Minute</a>
  </div>
  </div>
  </div>
  <div class="col-xl-5 col-lg-12 d-none d-xl-block col-md-6">
    <div class="banner-imgs">
      <div class="banner-1 shape-1">
        <img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse1.png') }}">
      </div>
      <div class="banner-2 shape-2">
        <img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse2.png') }}">
      </div>
      <div class="banner-3 shape-3">
        <img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse3.png') }}">
      </div>
      <div class="banner-4 shape-3">
        <img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse4.png') }}">
      </div>
      <div class="banner-5 shape-2">
        <img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse5.png') }}">
      </div>
      <div class="banner-6 shape-1">
        <img class="img-responsive" alt="jobBox" src="{{ asset('nurse/assets/imgs/nurse6.png') }}">
      </div>
    </div>
  </div>
  </div>
  </div>
  </div>
  </section> --}}
        {{-- <section class="section-box mt-70 mb-40">
									<div class="container">
										<div class="text-center">
											<h2 class="section-title mb-10 wow animate__animated animate__fadeInUp">You're crucial!</h2>
                    {{-- 
											<p class="font-lg color-text-paragraph-2 wow animate__animated animate__fadeInUp">Just via some simple steps, you will find your ideal candidates you’r looking for!" : You're crucial!</p> --}}
        {{-- <p class="font-lg color-text-paragraph-2 wow animate__animated animate__fadeInUp">With just a few simple
                        steps, you'll find the ideal nursing and midwifery jobs you are looking for!</p>
										</div>
										<div class="mt-70">
											<div class="row">
												<div class="col-lg-4">
													<div class="box-step step-1">
														<h1 class="number-element">1</h1>
														<h4 class="mb-10">You have the 
															<br>power
															</h4>
															<p class="font-lg color-text-paragraph-2">Customize your profile with personal 
																<br>
                                    information, certifications, preferences, specified qualifications, credentials,
																	<br>
                                    experience levels, references and any other relevant details.
                                
																	</p>
																</div>
															</div>
															<div class="col-lg-4">
																<div class="box-step step-2">
																	<h1 class="number-element">2</h1>
																	<h4 class="mb-10">We stay in 
																		<br>touch
																		</h4>
																		<p class="font-lg color-text-paragraph-2">Behind our software using latest 
																			<br> Ai
                                    technology, we are here to understand 
																				<br> your needs and help you to go 
																					<br>through
                                    this fast process. 
																					</p>
																				</div>
																			</div>
																			<div class="col-lg-4">
																				<div class="box-step">
																					<h1 class="number-element">3</h1>
																					<h4 class="mb-10">Receive tailored 
																						<br>job offers fast
																						</h4>
																						<p class="font-lg color-text-paragraph-2">Medical facilities and agencies 
																							<br> are actively
                                    competing, prompting 
																								<br> them to move quickly
																								</p>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div> --}}
        {{-- </section>  --}}
        {{-- <section class="section-box mt-50">
																			<div class="section-box wow animate__animated animate__fadeIn">
																				<div class="container">
																					<div class="text-center">
																						<h2 class="section-title mb-10 wow animate__animated animate__fadeInUp">Latest Jobs</h2>
																					</div>
																					<div class="row mt-50">
																						<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
																							<div class="card-grid-2 grid-bd-16 hover-up">
																								<div class="card-grid-2-image">
																									<span
                                        class="lbl-hot bg-green">
																										<span>Anaesthetics</span>
																									</span>
																									<div class="image-box">
																										<figure>
																											<img src="{{ asset('nurse/assets/imgs/page/homepage2/img1.png') }}" alt="jobBox"> </figure>
  </div>
  </div>
  <div class="card-block-info">
    <h5>
      <a href='#'>Anaesthetics</a>
    </h5>
    <div class="mt-5">
      <span class="card-location mr-15">New York, US</span>
      <span class="card-time">7:00 AM - 5:30 PM</span>
    </div>
    <div class="card-2-bottom mt-20">
      <div class="row">
        <div class="col-xl-7 col-md-7 mb-2">
          <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>
        </div>
        <div class="col-xl-5 col-md-5 text-lg-end">
          <span class="card-text-price">$90 - $120</span>
          <span class="text-muted">/Hour</span>
        </div>
      </div>
    </div>
    <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
  </div>
  </div>
  </div>
  <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card-grid-2 grid-bd-16 hover-up">
      <div class="card-grid-2-image">
        <span class="lbl-hot">
          <span>Full time</span>
        </span>
        <div class="image-box">
          <figure>
            <img src="{{ asset('nurse/assets/imgs/page/homepage2/img2.png') }}" alt="jobBox">
          </figure>
        </div>
      </div>
      <div class="card-block-info">
        <h5>
          <a href='#'>Gen Med/Gen Surg Ward</a>
        </h5>
        <div class="mt-5">
          <span class="card-location mr-15">New York, US</span>
          <span class="card-time">7:00 AM - 5:30 PM</span>
        </div>
        <div class="card-2-bottom mt-20">
          <div class="row">
            <div class="col-xl-7 col-md-7 mb-2">
              <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>
            </div>
            <div class="col-xl-5 col-md-5 text-lg-end">
              <span class="card-text-price">$80 - $150</span>
              <span class="text-muted">/Hour</span>
            </div>
          </div>
        </div>
        <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card-grid-2 grid-bd-16 hover-up">
      <div class="card-grid-2-image">
        <span class="lbl-hot">
          <span>Full time</span>
        </span>
        <div class="image-box">
          <figure>
            <img src="{{ asset('nurse/assets/imgs/page/homepage2/img3.png') }}" alt="jobBox">
          </figure>
        </div>
      </div>
      <div class="card-block-info">
        <h5>
          <a href='#'>Anaesthetics</a>
        </h5>
        <div class="mt-5">
          <span class="card-location mr-15">New York, US</span>
          <span class="card-time">7:00 AM - 5:30 PM</span>
        </div>
        <div class="card-2-bottom mt-20">
          <div class="row">
            <div class="col-xl-7 col-md-7 mb-2">
              <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>
            </div>
            <div class="col-xl-5 col-md-5 text-lg-end">
              <span class="card-text-price">$120 - $150</span>
              <span class="text-muted">/Hour</span>
            </div>
          </div>
        </div>
        <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card-grid-2 grid-bd-16 hover-up">
      <div class="card-grid-2-image">
        <span class="lbl-hot">
          <span>Full time</span>
        </span>
        <div class="image-box">
          <figure>
            <img src="{{ asset('nurse/assets/imgs/page/homepage2/img4.png') }}" alt="jobBox">
          </figure>
        </div>
      </div>
      <div class="card-block-info">
        <h5>
          <a href='#'>Gen Med/Gen Surg Ward</a>
        </h5>
        <div class="mt-5">
          <span class="card-location mr-15">New York, US</span>
          <span class="card-time">7:00 AM - 5:30 PM</span>
        </div>
        <div class="card-2-bottom mt-20">
          <div class="row">
            <div class="col-xl-7 col-md-7 mb-2">
              <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>
            </div>
            <div class="col-xl-5 col-md-5 text-lg-end">
              <span class="card-text-price">$80 - $150</span>
              <span class="text-muted">/Hour</span>
            </div>
          </div>
        </div>
        <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card-grid-2 grid-bd-16 hover-up">
      <div class="card-grid-2-image">
        <span class="lbl-hot">
          <span>Full time</span>
        </span>
        <div class="image-box">
          <figure>
            <img src="{{ asset('nurse/assets/imgs/page/homepage2/img5.png') }}" alt="jobBox">
          </figure>
        </div>
      </div>
      <div class="card-block-info">
        <h5>
          <a href='#'>Anaesthetics</a>
        </h5>
        <div class="mt-5">
          <span class="card-location mr-15">New York, US</span>
          <span class="card-time">7:00 AM - 5:30 PM</span>
        </div>
        <div class="card-2-bottom mt-20">
          <div class="row">
            <div class="col-xl-7 col-md-7 mb-2">
              <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>
            </div>
            <div class="col-xl-5 col-md-5 text-lg-end">
              <span class="card-text-price">$80 - $150</span>
              <span class="text-muted">/Hour</span>
            </div>
          </div>
        </div>
        <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card-grid-2 grid-bd-16 hover-up">
      <div class="card-grid-2-image">
        <span class="lbl-hot">
          <span>Full time</span>
        </span>
        <div class="image-box">
          <figure>
            <img src="{{ asset('nurse/assets/imgs/page/homepage2/img6.png') }}" alt="jobBox">
          </figure>
        </div>
      </div>
      <div class="card-block-info">
        <h5>
          <a href='#'>Gen Med/Gen Surg Ward</a>
        </h5>
        <div class="mt-5">
          <span class="card-location mr-15">New York, US</span>
          <span class="card-time">7:00 AM - 5:30 PM</span>
        </div>
        <div class="card-2-bottom mt-20">
          <div class="row">
            <div class="col-xl-7 col-md-7 mb-2">
              <a class='btn btn-tags-sm mr-5' href='#'>Start Application</a>
            </div>
            <div class="col-xl-5 col-md-5 text-lg-end">
              <span class="card-text-price">$80 - $150</span>
              <span class="text-muted">/Hour</span>
            </div>
          </div>
        </div>
        <p class="font-sm color-text-paragraph mt-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae architecto eveniet, dolor quo repellendus pariatur</p>
      </div>
    </div>
  </div>
  </div>
  </div>
  </div>
  </section> --}}
        <!-- <section class="section-box overflow-visible mt-50 mb-0 bg-cat2"><div class="container"><div class="row"><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12"><div class="text-center"><h1 class="color-brand-2"><span class="count">25</span><span> K+</span></h1><h5>Completed Cases</h5><p class="font-sm color-text-paragraph mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p></div></div><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12"><div class="text-center"><h1 class="color-brand-2"><span class="count">17</span><span> +</span></h1><h5>Our Office</h5><p class="font-sm color-text-paragraph mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p></div></div><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12"><div class="text-center"><h1 class="color-brand-2"><span class="count">86</span><span> +</span></h1><h5>Skilled People</h5><p class="font-sm color-text-paragraph mt-10">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo.</p></div></div><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12"><div class="text-center"><h1 class="color-brand-2"><span class="count">28</span><span> +</span></h1><h5>Happy Clients</h5><p class="font-sm color-text-paragraph mt-10">We always provide people a <br class="d-none d-lg-block">complete solution upon focused of <br class="d-none d-lg-block">any business</p></div></div></div></div></section> -->
        {{-- <section class="section-box overflow-visible mt-80 mb-100">
																									<div class="container">
																										<div class="row">
																											<div class="col-lg-6 col-sm-12">
																												<div class="box-image-job">
																													<!-- <img class="img-job-1" alt="jobBox" src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png') }}"> --> {{-- <img class="img-job-2" alt="jobBox" src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png')}}"> --}} {{-- <figure class="wow animate__ animate__fadeIn animated"
                                style="visibility: visible; animation-name: fadeIn;">
																															<img alt="jobBox"
                                    src="{{ asset('nurse/assets/imgs/img1.png') }}"> </figure>
  </div>
  </div>
  <div class="col-lg-6 col-sm-12">
    <div class="content-job-inner">
      <h2 class="text-52 wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">You have the power, receive tailored job offers fast!</h2>
      <div class="mt-20 pr-50 text-md-lh28 wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">Accept or reject interview invitations. Evaluate multiple offers from leading hospitals. Select the best match for your needs.</div>
      <div class="mt-20">
        <div class="wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
          <a class="btn btn-default" href='{{ route('nurse.login') }}'>Get Interviews</a>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div> --}} {{-- </section>  --}}
        {{-- <section class="section-box bg-15 pt-50 pb-50 mt-80">
																											<div class="container">
																												<div class="row align-items-center">
																													<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 text-center">
																														<img class="img-job-search mt-20"
                            src="{{ asset('nurse/assets/imgs/page/homepage3/img-job-search.png') }}" alt="jobBox"> </div>
  <div class="col-xl-5 col-lg-6 col-md-12 col-sm-12">
    <h2 class="mb-40 text-white">Job search for people passionate about startup</h2>
    <div class="box-checkbox mb-30">
      <h6 class="text-white">Create an account</h6>
      <p class="text-white font-sm opacity_6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo. </p>
    </div>
    <div class="box-checkbox mb-30">
      <h6 class="text-white">Search for Jobs</h6>
      <p class="text-white font-sm opacity_6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo. </p>
    </div>
    <div class="box-checkbox mb-30">
      <h6 class="text-white">Save &amp; Apply</h6>
      <p class="text-white font-sm opacity_6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec justo a quam varius maximus. Maecenas sodales tortor quis tincidunt commodo. </p>
    </div>
  </div>
  </div>
  </div>
  </section> --}} {{-- New Home page 9/03 --}}
        <div>
            <!-- HERO -->
            {{-- <section class="hero py-5">
																													<div class="container">
																														<div class="row align-items-center">
																															<div class="col-xl-7 col-lg-12">
																																<h1 class="hero-title">Find Jobs That Actually Match You</h1>
																																<p class="hero-text"> Powered by your preferences, credentials, and flexibility.</p>
																																<p class="hero-text mt-4"> 
                                No more endless scrolling. Mediqe uses your profile Compliance and availability to show the roles you're most likely t get.
                            </p>
																																<a class="create-btn mt-3">Create Your Free Profile</a>
																															</div>
																															<div class="col-xl-5 col-lg-12 d-none d-xl-block col-md-6">
																																<img src="https://images.unsplash.com/photo-1582750433449-648ed127bb54" class="hero-img">
																																</div>
																															</div>
																														</div>
																													</section> --}}
            <section class="section-box mb-20">
                <div class="banner-hero hero-1 banner-homepage5">
                    <div class="banner-inner container">
                        <div class="row align-items-center new-hero-section">
                            <div class="col-xl-7 col-lg-12">
                                <div class="block-banner pt-0">
                                    <div class="box-search-2 mb-40">
                                        <div class="block-banner p-0">
                                            <div class="form-find  wow animate__animated animate__fadeIn w-100"
                                                data-wow-delay=".2s">
                                                <div class="hero-wrapper">
                                                    <h1 class="hero-title">Find Jobs That Actually Match You</h1>
                                                    <p class="hero-text"> Powered by your preferences, credentials, and
                                                        flexibility.</p>
                                                    <p class="hero-text mt-4"> No more endless scrolling. Mediqe uses your
                                                        profile Compliance and availability to show the roles you're most
                                                        likely t get. </p>
                                                    <a class="create-btn mt-3 btn">Create Your Free Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-12 d-none d-xl-block col-md-6">
                                <div class="banner-imgs">
                                    <div class="banner-1 shape-1">
                                        <img class="img-responsive" alt="jobBox"
                                            src="{{ asset('nurse/assets/imgs/nurse1.png') }}">
                                    </div>
                                    <div class="banner-2 shape-2">
                                        <img class="img-responsive" alt="jobBox"
                                            src="{{ asset('nurse/assets/imgs/nurse2.png') }}">
                                    </div>
                                    <div class="banner-3 shape-3">
                                        <img class="img-responsive" alt="jobBox"
                                            src="{{ asset('nurse/assets/imgs/nurse3.png') }}">
                                    </div>
                                    <div class="banner-4 shape-3">
                                        <img class="img-responsive" alt="jobBox"
                                            src="{{ asset('nurse/assets/imgs/nurse4.png') }}">
                                    </div>
                                    <div class="banner-5 shape-2">
                                        <img class="img-responsive" alt="jobBox"
                                            src="{{ asset('nurse/assets/imgs/nurse5.png') }}">
                                    </div>
                                    <div class="banner-6 shape-1">
                                        <img class="img-responsive" alt="jobBox"
                                            src="{{ asset('nurse/assets/imgs/nurse6.png') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- FILTER TAGS -->
            <section>
                <div class="container py-5">
                    <div class="row text-center filters py-4 bg-white shadow-white">
                        <div class="col-md-2">
                            <div class="filter-item">
                                {{-- ⭐ --}}
                                <div>
                                    <i class="fa fa-star star" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4>Top Matches</h4>
                                    <p>Based on profile</p>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <div class="filter-item">
                                {{-- ✔  --}}
                                <div class="circle-check">
                                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4>Instant Connect</h4>
                                    <p>Same day shifts</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-item">
                                {{-- ⏱  --}}
                                <div class="clock circle-check">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4> Last Minute</h4>
                                    <p>Within 48h</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-item">
                                {{-- ▶ --}}
                                <div class="circle-check">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4> Immediate Start </h4>
                                    <p>Within 7 days</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-item">
                                {{-- ▶ --}}
                                <div class="circle-check">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4> Urgent Hire </h4>
                                    <p>Within 7 days</p>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-item">
                                {{-- ＋  --}}
                                <div class="circle-check">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4> New </h4>
                                    <p>Recently posted</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row text-center filters py-2">
          <div class="filter-item col-md-2">
            <p>Based on profile</p>
          </div>
          <div class="filter-item col-md-2">
            <p>Same day shifts</p>
          </div>
          <div class="filter-item col-md-2">
            <p>Within 48h</p>
          </div>
          <div class="filter-item col-md-2">
            <p>Within 7 days</p>
          </div>
          <div class="filter-item col-md-2">
            <p>Recently posted</p>
          </div>
        </div> --}}
                </div>
            </section>
            <!-- WHY SECTION -->
            <section class="py-5 bg-white">
                <div class="container ">
                    <h2 class="text-center">You're crucial!</h2>
                    <div class="row d-flex mt-15">
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="text-center">
                                    <svg width="90" height="90" viewBox="0 0 120 120">
                                        <!-- Shield background -->
                                        <path d="M60 10 L100 28 V60 C100 85 78 104 60 110 C42 104 20 85 20 60 V28 Z"
                                            fill="#9fd6d3" stroke="#6fb8b5" stroke-width="4" />
                                        <!-- Inner shield -->
                                        <path d="M60 25 L85 36 V58 C85 72 72 85 60 90 C48 85 35 72 35 58 V36 Z"
                                            fill="#2b7c84" />
                                        <!-- Medical cross -->
                                        <circle cx="80" cy="70" r="10" fill="#ffffff" />
                                        <rect x="78" y="64" width="4" height="12" fill="#2b7c84" />
                                        <rect x="74" y="68" width="12" height="4" fill="#2b7c84" />
                                    </svg>
                                </div>
                                <h4>Your Specialty. Your Growth. Your Environment.</h4>

                                <div class="nurse-card">
                                    <h5>
                                        <i class="fa fa-user-md mr-2" aria-hidden="true"></i> Type of Nurse
                                    </h5>
                                    <ul>
                                        <li>Primary Specialty</li>
                                        <li>Primary Specialty</li>
                                        <a href="#" data-toggle="modal" data-target="#filterModal">
                                            see more
                                        </a>
                                    </ul>
                                </div>
                                <hr class="hr-bg">
                                <div class="nurse-card">
                                    <h5>
                                        <i class="fa fa-level-up" aria-hidden="true"></i> Willing to Upskill
                                    </h5>
                                    <ul>
                                        <li>Location </li>
                                        <li>Primary Specialty</li>
                                    </ul>
                                </div>

                                <hr class="hr-bg">

                                <div class="nurse-card">
                                    <h5>
                                        <i class="fa fa-graduation-cap" aria-hidden="true"></i> Graduate Friendly
                                    </h5>
                                    <ul>
                                        <li>Residencies</li>
                                        <li>Fellowships</li>
                                        <li>Sponsorship</li>
                                    </ul>
                                </div>
                                <hr class="hr-bg">
                                <div class="nurse-card">
                                    <h5>
                                        <i class="fa fa-users" aria-hidden="true"></i> Work Environment
                                    </h5>
                                    <ul>
                                        <li>Location </li>
                                        <li>Preferences</li>
                                    </ul>
                                </div>

                                {{-- <ul>
																																									<li>Type of Nurse</li>
																																									<li>Primary Specialty</li>
																																									<li>Graduate Friendly</li>
																																									<li>Work Environment</li>
																																								</ul> --}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="text-center">
                                    <svg width="90" height="90" viewBox="0 0 120 120">
                                        <!-- Heart -->
                                        <path d="M60 22
                        C60 18 55 14 50 18
                        C45 22 48 30 60 38
                        C72 30 75 22 70 18
                        C65 14 60 18 60 22Z" fill="#ff6b6b" />
                                        <!-- Briefcase -->
                                        <rect x="25" y="45" width="70" height="45" rx="6"
                                            fill="#4a5f73" />
                                        <!-- Handle -->
                                        <rect x="45" y="35" width="30" height="12" rx="4"
                                            fill="#4a5f73" />
                                        <!-- Lock -->
                                        <rect x="56" y="65" width="8" height="8" fill="#f6c64f" />
                                    </svg>
                                </div>
                                <div class="nurse-card">
                                    <h4>Work That Fits Your Life</h4>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="nurse-card">
                                            <h5>
                                                <i class="fa fa-plus-square" aria-hidden="true"></i>Sector
                                            </h5>
                                            <ul>
                                                <li>Public</li>
                                                <li>Government</li>
                                                <li>Private</li>
                                            </ul>
                                        </div>
                                        <div class="nurse-card">
                                            <h5>
                                                <i class="fa fa-file-text-o" aria-hidden="true"></i> Employment Type
                                            </h5>
                                            <ul>
                                                <li>Permanent </li>
                                                <li>Fixed-term </li>
                                                <li>Temporary </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- <hr class="hr-bg"> -->
                                    <hr class="hr-bg">
                                    <div class="nurse-card">
                                        <h5>
                                            <i class="fa fa-bars" aria-hidden="true"></i>Shift Structure
                                        </h5>
                                        <ul>
                                            <li>Shift Types</li>
                                            <li>Shift Length</li>
                                            <li>Schedule Model</li>
                                            <li>Weekly Work Patterns</li>
                                            <li>Shift Rotation & Cycle</li>
                                            <li>Non-Traditional Shift </li>
                                            <li>Maternity & Midwifery Shift</li>
                                            <li>Days Off</li>
                                            <li>Specific Days Off</li>
                                        </ul>
                                    </div>
                                    <!-- <ul><li>Public / Government / Private</li><li>Permanent / Temporary</li><li>Shift Structure</li><li>Schedule Model</li></ul> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="text-center">
                                    <svg width="90" height="90" viewBox="0 0 120 120">
                                        <!-- Clipboard -->
                                        <rect x="35" y="30" width="55" height="70" rx="8" fill="#eef1f5"
                                            stroke="#b8c0cc" stroke-width="3" />
                                        <!-- Clip -->
                                        <rect x="50" y="20" width="25" height="15" rx="4"
                                            fill="#5a6573" />
                                        <!-- Paper lines -->
                                        <line x1="45" y1="55" x2="80" y2="55"
                                            stroke="#8a94a3" stroke-width="3" />
                                        <line x1="45" y1="68" x2="80" y2="68"
                                            stroke="#8a94a3" stroke-width="3" />
                                        <line x1="45" y1="81" x2="70" y2="81"
                                            stroke="#8a94a3" stroke-width="3" />
                                        <!-- Coin -->
                                        <circle cx="40" cy="60" r="15" fill="#f6c64f" />
                                        <text x="40" y="65" text-anchor="middle" font-size="16" fill="#2c3e50">$</text>
                                    </svg>
                                </div>
                                <div class="nurse-card">
                                    <h4>Extra Tags</h4>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="nurse-card">
                                            <h5>
                                                <i class="fa fa-suitcase" aria-hidden="true"></i>Financial
                                            </h5>
                                            <ul>
                                                <li>Overtime</li>
                                                <li>Shift Loading </li>
                                                <li>Bonuses</li>
                                                <li>Sign-On Bonus</li>
                                            </ul>
                                        </div>
                                        <div class="nurse-card">
                                            <h5>
                                                <i class="fa fa-file" aria-hidden="true"></i> Work-Life:
                                            </h5>
                                            <ul>
                                                <li>Flexible Rosters </li>
                                                <li>Self-Scheduling</li>
                                                <li>Paid Time Off</li>
                                                <li>Work-from-Home</li>
                                                <li>Childcare</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="hr-bg">
                                    <div class="d-flex gap-3 mt-3 flex-wrap">
                                        <div class="nurse-card">
                                            <h5>
                                                <i class="fa fa-sun-o" aria-hidden="true"></i> Career Growth
                                            </h5>
                                            <ul>
                                                <li>Paid CPD </li>
                                                <li>Residencies </li>
                                                <li>Fellowships </li>
                                                <li>Graduate </li>
                                                <li>Friendly</li>
                                                <li>Student Friendly </li>
                                            </ul>
                                        </div>
                                        <div class="nurse-card">
                                            <h5>
                                                <i class="fa fa-globe" aria-hidden="true"></i> Travel & Support
                                            </h5>
                                            <ul>
                                                <li>Relocation </li>
                                                <li>Housing</li>
                                                <li>Travel Allowance</li>
                                                <li>Sponsorship</li>
                                                <li>Paid Flights </li>
                                                <li>Car allowance</li>
                                                <li>Fuel allowance </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="hr-bg">
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="nurse-card">
                                            <h5>
                                                <i class="fa fa-lock" aria-hidden="true"></i> Protection
                                            </h5>
                                            <ul>
                                                <li>PII</li>
                                                <li>EAP</li>
                                                <li>PPE Provided</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="bg-white overflow-visible py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="box-image-job">
                                {{-- <!-- <img class="img-job-1" alt="jobBox" src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png') }}"> --> --}}
                                {{-- <img class="img-job-2" alt="jobBox"
                                    src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png') }}"> --}}
                                <figure class="wow animate__ animate__fadeIn animated"
                                    style="visibility: visible; animation-name: fadeIn;">
                                    <img alt="jobBox" src="{{ asset('nurse/assets/imgs/stop-scroll.jpg') }}">
                                </figure>
                            </div>
                        </div>
                         <div class="col-lg-6 col-sm-12">
                            <div class="content-job-inner">
                                <h2 class="text-30 wow animate__ animate__fadeInUp animated"
                                    style="visibility: visible; animation-name: fadeInUp;"> Stop scrolling </h2>
                                     <h2 class="text-30 wow animate__ animate__fadeInUp animated"
                                    style="visibility: visible; animation-name: fadeInUp;"> Start matching </h2>
                                <div class="mt-20 pr-50 text-md-lh28 wow animate__ animate__fadeInUp animated"
                                    style="visibility: visible; animation-name: fadeInUp;">
                                    <div class="stop">
                                        <h4>Create Your <span> <a href="#"> Professional Profile </a></span></h4>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>


                </div>

            </section>
            <!-- LATEST JOBS -->
            <section class="jobs py-5">
                <div class="container">
                    <h2 class="text-center">Latest Jobs</h2>
                    <div class="row mt-15">
                        <div class="col-md-4">
                            <div class="job-card">
                                <div class="latest-job-card">
                                    <img src="https://images.unsplash.com/photo-1581594693702-fbdc51b2763b"
                                        class="img-fluid">
                                    <div class="top-note">
                                        <p>Permanent</p>
                                    </div>
                                </div>
                                <div class="job-content">
                                    <h5>Graduate Registered Midwife</h5>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <p class="job-price">$60/hr</p>
                                        <p class="job-right-content">$60/hr</p>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-block"> Complete Credentials to Apply </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="job-card">
                                <div class="latest-job-card">
                                    <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095"
                                        class="img-fluid">
                                    <div class="top-note">
                                        <p>Fixed Term </p>
                                    </div>
                                </div>
                                <div class="job-content">
                                    <h5>Assistant in Nursing — Medical</h5>
                                    {{-- <p class="job-price">$14000 - 15000</p> --}}
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <p class="job-price">$14000 - 15000</p>
                                        <p class="job-right-content">$60/hr</p>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-block"> Complete Credentials to Apply </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="job-card">
                                <div class="latest-job-card">
                                    <img src="https://images.unsplash.com/photo-1537368910025-700350fe46c7"
                                        class="img-fluid">
                                    <div class="top-note">
                                        <p>From temporary </p>
                                    </div>
                                </div>
                                <div class="job-content">
                                    <h5>Enrolled Nurse – Paediatrics</h5>
                                    {{-- <p class="job-price">$25 - 40k</p> --}}
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <p class="job-price">$25 - 40k</p>
                                        <p class="job-right-content">$60/hr</p>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-block"> Complete Credentials to Apply </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-box overflow-visible mt-80 mb-100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="box-image-job">
                                {{-- <!-- <img class="img-job-1" alt="jobBox" src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png') }}"> --> --}}
                                <img class="img-job-2" alt="jobBox"
                                    src="{{ asset('nurse/assets/imgs/page/homepage1/img-chart.png') }}">
                                <figure class="wow animate__ animate__fadeIn animated"
                                    style="visibility: visible; animation-name: fadeIn;">
                                    <img alt="jobBox" src="{{ asset('nurse/assets/imgs/img1.png') }}">
                                </figure>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="content-job-inner">
                                <h2 class="text-30 wow animate__ animate__fadeInUp animated"
                                    style="visibility: visible; animation-name: fadeInUp;">You’re Crucial. Your Career
                                    Should Be Precise. </h2>
                                <div class="mt-20 pr-50 text-md-lh28 wow animate__ animate__fadeInUp animated"
                                    style="visibility: visible; animation-name: fadeInUp;">
                                    <div class="credential">
                                        <p><i class="fa fa-check-circle" aria-hidden="true"></i> Your credentials matter
                                        </p>
                                        <p><i class="fa fa-check-circle" aria-hidden="true"></i> Your flexibility matters
                                        </p>
                                        <p><i class="fa fa-check-circle" aria-hidden="true"></i> Your preferences matter
                                        </p>
                                        <h4>Mediqa makes sure the right facilities see that.</h4>
                                    </div>


                                </div>
                                <div class="mt-20">
                                    <div class="wow animate__ animate__fadeInUp animated"
                                        style="visibility: visible; animation-name: fadeInUp;">
                                        <a class="btn btn-default" href='{{ route('nurse.login') }}'>Create Your
                                            Professional Profile</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        </div>


        <div class="modal fade right-modal" id="filterModal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Filters</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Your content here
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('nurse/assets/js/plugins/counterup.js') }}"></script>
    </main>
    @endsection @section('js')
@endsection
