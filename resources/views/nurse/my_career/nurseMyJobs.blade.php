@extends('nurse.layouts.layout')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ url('/public') }}/nurse/assets/css/jquery.ui.datepicker.monthyearpicker.css">
<link rel='stylesheet'
  href='https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<style type="text/css">
  .hide_profile_image {
    display: none !important;
  }
  span.select2.select2-container {
    padding: 5px !important;
    width: 100% !important;
  }
  .select2-container--default .select2-selection--multiple {
    background-color: white !important;
    border: 1px solid #0000 !important;
    border-radius: 4px !important;
    cursor: text !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #000 !important;
    border: 1px solid #000 !important;
    border-radius: 4px !important;
    cursor: default !important;
    color: #fff !important;
    float: left;
    padding: 0;
    padding-right: 0.75rem;
    margin-top: calc(0.375rem - 2px);
    margin-right: 0.375rem;
    padding-bottom: 2px;
    white-space: normal;
    line-height: 20px;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    font-size: 20px !important;
    float: left;
    padding-right: 3px;
    padding-left: 3px;
    margin-right: 1px;
    margin-left: 3px;
    font-weight: 700;
    line-height: 20px;
  }
  .registration_progress {
    font-weight: 900;
    background-color: black;
    color: #fff;
  }
  form#multi-step-form-nurseProfileForm ul.select2-selection__rendered {
    box-shadow: none;
    max-height: inherit;
    border: none;
    position: relative;
  }
  .category {
    margin-bottom: 1.5rem;
  }
  .label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.4rem;
    font-weight: 600;
  }
  .progress-bar-bg {
    background: #e4e8ee;
    height: 16px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
  }
  .progress-bar-fill {
    background: #000;
    height: 100%;
    border-radius: 10px;
    color: white;
    font-size: 12px;
    padding-left: 6px;
    display: flex;
    align-items: center;
  }
  /*07/02 */
  .status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    /*white-space: nowrap;*/
    display: inline-block;
    text-align: center;
    min-width: 145px;
  }
  .under-review {
    background: #facc15;
    color: #856404;
  }
  .offer {
    background: #22c55e;
    color: #155724;
  }
  .shortlisted {
    background: #ef4444;
    color: #ffffff;
  }
  /* Right side modal */
  .modal.right .modal-dialog {
    position: fixed;
    right: 0;
    margin: 0;
    width: 420px;
    height: 100%;
    transform: translate3d(0%, 0, 0);
  }
  .modal.right .modal-content {
    height: 100%;
    border-radius: 0;
    border: none;
  }
  .timeline {
    position: relative;
    padding-left: 30px;
  }
  .timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 8px;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
  }
  .timeline-item {
    position: relative;
    margin-bottom: 20px;
  }
  .timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 8px;
    width: 10px;
    height: 10px;
    background: #facc15;
    border-radius: 50%;
  }
  .pl-370 {
    padding-left: 370px !important;
  }
  .application .nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 2px solid #000000;
    border-top: 0;
    border-left: 0;
    border-right: 0;
    border-radius: 0;
  }
  .application .nav-tabs .nav-link:focus,
  .nav-tabs .nav-link:hover {
    border-color: transparent;
    isolation: isolate;
  }
  .filter-btn {
    /*border: 1px solid #e5e7eb;*/
    font-size: 14px;
    padding: 6px 12px;
    background: #fff;
  }
  .filter-btn:hover {
    background: #f8f9fa;
  }
  .search-input {
    padding-left: 32px;
    height: 38px;
    font-size: 14px;
  }
  .search-icon {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
  }
  .filter-border {
    border: 1px solid #dee2e6;
    border-radius: 10px;
  }
  .filter-border .btn-light {
    border-color: transparent;
    padding-left: 10px;
  }
  .filter-border input {
    border: 0;
    padding-left: 32px;
    height: auto !important;
    padding: 11px 35px;
  }
  .filter-item:not(:last-child) {
    border-right: 1px solid #e5e7eb;
    padding: 7px 15px;
  }
  .filter-item i {
    margin-right: 8px !important;
  }
  .whitespace-nowrap {
    white-space: nowrap;
    padding: 7px 15px;
  }
  .pt-7 {
    padding: 7px 0;
  }
  .pl-20 {
    padding-left: 20px;
  }
  .application-table th {
    color: #6b7280;
  }
  .application-table .table-bordered,
  .table-bordered td {
    border: 1px solid #dee2e6 !important;
    padding: 15px;
  }
  .application-table .table-bordered>:not(caption)>*>* {
    border-width: inherit;
  }
  .application-table thead {
    border-bottom: transparent;
  }
  .table-nurse-head {
    font-size: 16px;
    color: #000000;
  }
  .application-table small {
    font-size: 12px !important;
  }
  .timeline-item small {
    font-size: 12px;
    color: #6b7280;
  }
  .progress-content {
    background: #60a5fa14;
    width: auto;
    padding: 20px;
    border-radius: 20px;
  }
  .close {
    border: 0;
    background: transparent;
  }
  .pending-offer-head {
    color: #60a5fa;
  }
  .pending-des {
    position: relative;
    padding-left: 15px;
    max-width: 160px;
    font-size: 12px;
  }
  .pending-des::before {
    content: '';
    position: absolute;
    left: 0;
    top: 10px;
    background: #6b7280;
    width: 8px;
    height: 8px;
    border-radius: 100%;
  }
  .status-badge.submitted {
    color: white;
    background: #6b7280;
  }
  .status-badge.under_review {
    color: white;
    background: #facc15;
  }
  .status-badge.shortlisted {
    color: white;
    background: #f59e0b;
  }
  .status-badge.interview_scheduled {
    color: white;
    background: #3b82f6;
  }
  .status-badge.interview_completed {
    color: white;
    background: #60a5fa;
  }
  .status-badge.conditional_offer {
    color: white;
    background: #3b82f6;
  }
  .status-badge.offer {
    color: white;
    background: #22c55e;
  }
  .status-badge.hired {
    color: white;
    background: #8b5cf6;
  }
  .status-badge.withdrawn {
    color: white;
    background: #374151;
  }
  .status-badge.rejected,
  .status-badge.declined {
    color: white;
    background: #ef4444;
  }
  /*10/02 */
  .offer-modal {
    border-radius: 12px;
  }
  .accept-box {
    background: linear-gradient(135deg, #eef8ff, #f9fcff);
  }
  .status-box {
    background: #f5f7f9;
  }
  .badge-success {
    background-color: #22b573;
  }
  .modal-footer .btn-success {
    background-color: #22b573;
    border: none;
  }
  /*view interview details */
  .process-modal {
    border-radius: 12px;
  }
  .interview-status {
    background: #fff3cd;
    border: none;
    border-radius: 8px;
    color: #664d03;
  }
  .process {
    position: relative;
    padding-left: 30px;
  }
  .process::before {
    content: "";
    position: absolute;
    left: 6px;
    top: 6px;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
  }
  .process-item {
    position: relative;
    display: flex;
    margin-bottom: 20px;
  }
  .process-item.last {
    margin-bottom: 0;
  }
  .process-item .dot {
    position: absolute;
    left: -30px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #ced4da;
    margin-top: 6px;
  }
  .process-item.active .dot {
    background: #007bff;
  }
  .process-item.orange .dot {
    background: #f0ad4e;
  }
  .process-item .content {
    padding-left: 10px;
  }
  .withdraw-text {
    font-size: 12px;
  }
  /*view details */
  .offer-review-modal {
    border-radius: 12px;
  }
  /* Left card */
  .left-card {
    background: #f8f9fa;
    /*border-radius: 10px;*/
  }
  /* Status alert */
  .status-alert {
    background: #fff4da;
    border-radius: 8px;
    padding: 14px 16px;
    color: #6b4e00;
  }
  /* Document cards */
  .doc-card {
    background: #f4f9fd;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  #offerReviewModal small {
    font-size: 12px;
  }
  /*11/02 */
  /* Modal width like mobile design */
  .withdrawn-modal .modal-dialog {
    max-width: 580px;
  }
  .withdrawn-modal .modal-content {
    border-radius: 12px;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  }
  /* Header */
  .withdrawn-title {
    font-size: 18px;
    font-weight: 600;
    color: #212529;
  }
  /* Withdrawn pill top */
  .withdrawn-pill {
    display: inline-flex;
    align-items: center;
    background: #2f3e46;
    color: #fff;
    font-size: 13px;
    padding: 6px 14px;
    border-radius: 20px;
    margin-top: 10px;
    width: fit-content;
  }
  .withdrawn-pill::before {
    content: "−";
    /*display: inline-block;*/
    /* font-weight: bold; */
    margin-right: 6px;
    background: #ffffff;
    height: 16px;
    border-radius: 100%;
    width: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #000000;
  }
  /* Info box */
  .withdrawn-info {
    background: #f1f1f1;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    margin-top: 15px;
    color: #495057;
  }
  /* Timeline */
  .withdrawn-timeline {
    position: relative;
    margin-top: 20px;
    padding-left: 30px;
  }
  .withdrawn-timeline::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 4px;
    bottom: 4px;
    width: 2px;
    background: #dee2e6;
  }
  .withdrawn-step {
    position: relative;
    margin-bottom: 22px;
    font-size: 12px;
    color: #212529;
  }
  .withdrawn-step:last-child {
    margin-bottom: 0;
  }
  .withdrawn-dot {
    position: absolute;
    left: -31px;
    top: 4px;
    width: 20px;
    height: 20px;
    background: #adb5bd;
    border-radius: 50%;
    border: 2px solid #fff;
    font-size: 12px;
  }
  .withdrawn-step small {
    font-size: 12px;
    color: #6c757d;
  }
  .withdrawn-step .withdrawn-date {
    float: right;
    font-size: 12px;
    color: #6c757d;
  }
  /* Withdrawn active step */
  .withdrawn-step.withdrawn-active .withdrawn-dot {
    background: #2f3e46;
  }
  .withdrawn-label {
    background: #2f3e46;
    color: #fff;
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 20px;
    margin-right: 8px;
  }
  /* Job Card */
  .withdrawn-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 16px;
    margin-top: 22px;
    font-size: 12px;
  }
  .withdrawn-card .withdrawn-status {
    background: #2f3e46;
    color: #fff;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
    margin-left: 10px;
  }
  .withdrawn-card hr {
    margin: 12px 0;
  }
  .withdrawn-card a {
    display: block;
    text-align: center;
    color: #007bff;
    font-weight: 500;
    text-decoration: none;
  }
  /* Footer */
  .withdrawn-footer {
    margin-top: 15px;
    /*display: flex;
      justify-content: flex-end;*/
  }
  .withdrawn-footer .btn {
    border-radius: 8px;
    padding: 6px 18px;
  }
  /*new */
  /* Make circle center content */
  .withdrawn-dot {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  /* Hide icon by default */
  .withdrawn-dot i {
    display: none;
    color: #fff;
    font-size: 8px;
  }
  /* Show check for completed steps */
  .withdrawn-step.completed .withdrawn-dot {
    background: #6c757d;
  }
  .withdrawn-step.completed .withdrawn-dot i {
    display: block;
  }
  /*archived reject modal */
  /* Modal width like mobile design */
  .rejected-modal .modal-dialog {
    max-width: 580px;
  }
  .rejected-modal .modal-content {
    border-radius: 12px;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  }
  /* Header */
  .rejected-header {
    text-align: center;
    font-weight: 600;
    font-size: 18px;
    position: relative;
  }
  .rejected-header .close {
    position: absolute;
    right: 0;
    top: 0;
  }
  /* Title */
  .rejected-title {
    font-size: 18px;
    font-weight: 600;
    /*margin-top: 15px;*/
  }
  /* Status pill */
  .rejected-pill {
    display: inline-flex;
    align-items: center;
    background: #ef4444;
    color: #fff;
    font-size: 12px;
    padding: 3px 14px;
    border-radius: 20px;
    margin-top: 10px;
    width: fit-content;
  }
  .rejected-pill i {
    margin-right: 6px;
  }
  /* Rejected message box */
  .rejected-alert {
    background: #fdecea;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
  }
  .rejected-alert p {
    font-size: 12px;
    line-height: normal;
  }
  .rejected-alert strong {
    display: block;
    margin-bottom: 5px;
  }
  /* Timeline */
  .rejected-timeline {
    position: relative;
    margin-top: 20px;
    padding-left: 30px;
  }
  .rejected-timeline::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 4px;
    bottom: 4px;
    width: 2px;
    background: #dee2e6;
  }
  .rejected-step {
    position: relative;
    margin-bottom: 20px;
    font-size: 12px;
  }
  .rejected-step:last-child {
    margin-bottom: 0;
  }
  .rejected-dot {
    position: absolute;
    left: -31px;
    top: 3px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .rejected-step.completed .rejected-dot {
    background: #6c757d;
  }
  .rejected-step.completed .rejected-dot i {
    color: #fff;
    font-size: 10px;
  }
  .rejected-step.rejected-active .rejected-dot {
    background: #ef4444;
  }
  .rejected-step.rejected-active .rejected-dot i {
    color: #fff;
    font-size: 10px;
  }
  .rejected-date {
    float: right;
    font-size: 12px;
    color: #6c757d;
  }
  /* Job Card */
  .rejected-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    margin-top: 20px;
    font-size: 12px;
  }
  .rejected-status {
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
    margin-left: 6px;
  }
  .rejected-card a {
    display: block;
    text-align: center;
    color: #007bff;
    margin-top: 10px;
    font-weight: 500;
    text-decoration: none;
  }
  /* Footer */
  .rejected-footer {
    margin-top: 15px;
  }
  .rejected-footer btn {
    font-size: 12px;
  }
  /*archived hired modal */
  /* Modal width */
  .hired-modal .modal-dialog {
    max-width: 580px;
  }
  .hired-modal .modal-content {
    border-radius: 12px;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  }
  /* Header */
  .hired-header {
    text-align: center;
    font-weight: 600;
    font-size: 18px;
    position: relative;
  }
  .hired-header .close {
    position: absolute;
    right: 0;
    top: 0;
  }
  /* Title */
  .hired-title {
    font-size: 18px;
    font-weight: 600;
  }
  /* Purple pill */
  .hired-pill {
    display: inline-block;
    background: #8b5cf6;
    color: #fff;
    font-size: 13px;
    padding: 3px 16px;
    border-radius: 20px;
    margin-top: 10px;
    width: fit-content;
  }
  /* Message box */
  .hired-alert {
    background: #f2ecfb;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
  }
  .hired-alert p {
    font-size: 12px;
    line-height: normal;
  }
  .hired-alert strong {
    display: block;
    margin-bottom: 5px;
  }
  /* Timeline */
  .hired-timeline {
    position: relative;
    margin-top: 20px;
    padding-left: 30px;
  }
  .hired-timeline::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 4px;
    bottom: 4px;
    width: 2px;
    background: #dee2e6;
  }
  .hired-step {
    position: relative;
    margin-bottom: 18px;
    font-size: 12px;
  }
  .hired-dot {
    position: absolute;
    left: -31px;
    top: 3px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 10px;
  }
  .dot-green {
    background: #22c55e;
  }
  .dot-orange {
    background: #f59e0b;
  }
  .dot-blue {
    background: #2563eb;
  }
  .hired-date {
    float: right;
    font-size: 12px;
    color: #6c757d;
  }
  /* Job Card */
  .hired-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    margin-top: 20px;
    font-size: 12px;
  }
  .hired-status {
    background: #8b5cf6;
    color: #fff;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
  }
  /* Buttons */
  /*.hired-actions {
      margin-top: 15px;
    }*/
  .hired-actions .btn {
    font-size: 12px !important;
    padding: 10px 25px;
  }
  .hired-actions .btn-primary {
    background: #2563eb;
    border: none;
  }
  .hired-footer .btn {
    font-size: 12px;
    padding: 10px 25px;
  }

  /*16/02 */
   /*footer bottom fix */
    footer{
    position: fixed;
    width: 100%;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9;
    }
     .sidebar_profile{
      z-index: 99;
    }
    .filter-btn {
    /*border: 1px solid #e5e7eb;*/
    font-size: 14px;
    padding: 6px 12px;
    background: #fff;
  }

  .filter-btn:hover {
    background: #f8f9fa;
  }

  .search-input {
    padding-left: 32px;
    height: 38px;
    font-size: 14px;
  }

  .search-icon {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
  }

  .filter-border {
    border: 1px solid #dee2e6;
    border-radius: 10px;
  }

  .filter-border .btn-light {
    border-color: transparent;
    padding-left: 10px;
  }

  .filter-border input {
    border: 0;
    padding-left: 32px;
    height: auto !important;
    padding: 11px 35px;
    border-radius: 10px;
  }

  .filter-item:not(:last-child) {
    border-right: 1px solid #e5e7eb;
    padding: 7px 15px;
  }

  .filter-item i {
    margin-right: 8px !important;
  }
   .filter-item .form-control:focus {
    border: 0;
}
   .form-control:focus {
    border: 0 !important;
}
.filter-item input[type="text"]{
  padding-left: 35px;
  border-radius: 10px;
  border: 1px solid #dee2e6;
  height: auto;
  padding: 10px 12px 10px 32px;
}

.text-14{
      font-size: 14px;
    }
    .text-12{
      font-size: 12px;
    }
    .btn-xs {
  padding: 8px;
  font-size: 12px;
}

   .custom-tabs {
            border-bottom: 1px solid #eee;
        }

        .custom-tabs .nav-link {
            border: none;
            color: #555;
            font-weight: 500;
            position: relative;
            padding: 10px 20px;
        }

        .custom-tabs .nav-link.active {
            color: #000;
            background: transparent;
        }

        /* Desktop underline */
        @media (min-width: 768px) {
            .custom-tabs .nav-link.active::after {
                content: "";
                position: absolute;
                left: 0;
                bottom: -2px;
                width: 100%;
                height: 3px;
                background-color: #000;
                border-radius: 2px;
            }
        }

        /* Badge */
        .custom-badge {
            background-color: #ffc107;
            color: #000;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 20px;
            margin-left: 6px;
            font-weight: 600;
        }

        /* Right modal  */
        /* POSITION drawer on RIGHT */
        .modal-dialog-slideout {
            position: fixed;
            right: 0;
            top: 0;
            margin: 0;
            height: 100%;
            width: 100%;
            max-width: 440px;
        }

        /* FULL HEIGHT */
        .job-drawer-modal .modal-content {
            height: 100vh;
            border-radius: 0;
            border: none;
        }

        /* START hidden outside RIGHT */
        .job-drawer-modal.fade .modal-dialog {
            transform: translateX(100%);
            transition: transform 0.35s ease-in-out;
        }

        /* SLIDE IN (RIGHT → LEFT) */
        .job-drawer-modal.show .modal-dialog {
            transform: translateX(0);
        }

        /* Disable bootstrap vertical animation */
      .modal.fade .modal-dialog {
            transition: none;
        }

      
 /*      .job-drawer-modal.fade .modal-dialog {
    transform: translateX(100%);
    transition: transform 0.35s ease-in-out;
}

.job-drawer-modal.show .modal-dialog {
    transform: translateX(0);
}
.modal-backdrop.show {
    opacity: 0;
}*/

        /* Header */
        .drawer-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        /* Body scroll */
        .job-drawer-modal .modal-body {
            overflow-y: auto;
            height: calc(100vh - 140px);
        }

        /* Cards */
        .drawer-card {
            background: #f8f9fb;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        /* Footer */
        .drawer-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            background: #fff;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }


        /* Status step */
        .job-status span {
            /*margin-right: 15px;*/
            color: #999;
            font-size: 14px;
        }
        .active-circle{
           display: inline-block;
          width: 6px !important;
          height: 6px !important;
          background: #999;
          border-radius: 100%;
          margin-right: 5px;
        }
     

        .job-status .active-step {
            color: #28a745;
            font-weight: 600;
        }

        /* Logo */
        .job-logo {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
        }
        .badge-warning{
          background: #f97316;
        }
        .msg-dimension{
          width: 30px;
          height: 30px;
          border-radius: 100%;
          object-fit: cover;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .modal-dialog-slideout {
                max-width: 100%;
            }

            .custom-tabs {
                border-bottom: none;
                border-right: 1px solid #eee;
            }

            .custom-tabs .nav-link {
                text-align: left;
                border-left: 3px solid transparent;
            }

            .custom-tabs .nav-link.active {
                border-left: 3px solid #000;
                background-color: #f8f9fa;
            }
        }

</style>
@endsection
@section('content')
<main class="main">
  <section class="section-box mt-0">
    <div class="row m-0 profile-wrapper">
      <div class="col-lg-3 col-md-4 col-sm-12 p-0 left_menu">
        @include('nurse.layouts.career_sidebar')
      </div>
      <main class="main">
        <section class="section-box mt-0">
          <div class="row m-0 profile-wrapper">
            {{-- LEFT SIDEBAR --}}
            <div class="col-lg-3 col-md-4 col-sm-12 p-0 left_menu">
              @include('nurse.layouts.career_sidebar')
            </div>
            {{-- RIGHT CONTENT --}}
            <div class="container mt-5 pl-370 application">
              <div>
                 <h4 class="mb-0">My Jobs</h4>
              </div>
               <!-- Filters row (optional) -->
                <div class="d-flex w-100 align-items-center mb-2 gap-4 mt-4">
                    <!-- Left Filters -->
                    <div class="d-flex align-items-center filter-border w-100">
                      <!-- Status Dropdown -->
                      <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                          <span class=""><i class="fas fa-search"></span></i> Status
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Under Review</a>
                          <a class="dropdown-item" href="#">Offer</a>
                          <a class="dropdown-item" href="#">Shortlisted</a>
                          <a class="dropdown-item" href="#">Rejected</a>
                        </div>
                      </div>
                        <!-- Facility Dropdown -->
                      <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                          <span class="">
                            <!-- <i class="fas fa-search"></span></i> -->
                             Facility
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Under Review</a>
                          <a class="dropdown-item" href="#">Offer</a>
                          <a class="dropdown-item" href="#">Shortlisted</a>
                          <a class="dropdown-item" href="#">Rejected</a>
                        </div>
                      </div>
                      <!-- Date Range  -->
                      <div class="mr-2 filter-item p-0">
                           <input type="date" class="form-control mr-2 w-auto">
                      </div>
                       <div class="mr-2 filter-item p-0">
                           <input type="date" class="form-control mr-2 w-auto">
                      </div>
                       <!-- Date Dropdown -->
                      <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                          <!-- <i class="far fa-calendar-alt mr-1"></i> -->
                           Location
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Last 7 Days</a>
                          <a class="dropdown-item" href="#">Last 30 Days</a>
                          <a class="dropdown-item" href="#">Last 6 Months</a>
                        </div>
                      </div>
                       <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                           Speciality
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Last 7 Days</a>
                          <a class="dropdown-item" href="#">Last 30 Days</a>
                          <a class="dropdown-item" href="#">Last 6 Months</a>
                        </div>
                      </div>
                    </div>
                     
                        <!-- Speciality-->
                     <!--  <div class="dropdown mr-2 filter-item">
                        <button class="btn btn-light dropdown-toggle filter-btn" data-toggle="dropdown">
                           Speciality
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">Last 7 Days</a>
                          <a class="dropdown-item" href="#">Last 30 Days</a>
                          <a class="dropdown-item" href="#">Last 6 Months</a>
                        </div>
                      </div> -->
                  </div>
                  <div>
                    <h6>Shift type</h6>
                    <div class="d-flex gap-2">
                   <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                    <label class="form-check-label" for="defaultCheck1">
                    Morning
                    </label>
                  </div>
                   <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                    <label class="form-check-label" for="defaultCheck2">
                    Afternoon
                    </label>
                  </div>
                   <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck3">
                    <label class="form-check-label" for="defaultCheck3">
                    Night
                    </label>
                  </div>
                  </div>

                  </div>

         <div class="row">

            <!-- Tabs -->
            <div class="col-md-12">
                <ul class="nav flex-column flex-md-row custom-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#acceptedTab">
                            Accepted
                            <span class="custom-badge">3</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#onboardinTab">
                            Onboarding
                            <span class="custom-badge">2</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#activeShifts">
                            Active Shifts
                            <span class="custom-badge">1</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#completedJobTab">
                            Completed Jobs
                            <span class="custom-badge">8</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Content -->
            <div class="col-md-12 mt-3 mt-lg-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="acceptedTab">
                        
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="mt-4">
                          <div class="card job-card mb-3">
                            <div class="card-body d-flex align-items-center justify-content-between gap-4">
                              <div class="d-flex gap-2 align-items-center">

                                <img src="hospital.png" class="job-logo mr-3">

                                <div class="flex-grow-1">
                                    <h6 class="font-weight-bold">
                                        Canberra General Hospital
                                    </h6>

                                    <small class="text-12 text-muted">
                                        ICU Nurse – ICU
                                    </small>

                                    <div class="text-12 text-muted small">
                                        Thu, 18 Apr 2026 • $65/hr
                                    </div>
                                </div>
                                </div>

                                <button class="btn btn-primary btn-sm text-12 open-job-drawer"
                                 >
                                    View Details
                                </button>

                            </div>
                        </div>
                        </div>
                          </div>
                          
                        </div>

                    </div>
                    <div class="tab-pane fade" id="onboardinTab">
                        <p>onboarding content goes here...</p>
                    </div>
                    <div class="tab-pane fade" id="activeShifts">
                        <p>Active Shift content goes here...</p>
                    </div>
                    <div class="tab-pane fade" id="completedJobTab">
                        <p>Complete job content goes here...</p>
                    </div>
                </div>
            </div>

        </div>   
                </div>


            </div>
        </section>
      </main>
    </div>
  </section>
</main>

<!-- view Details modal  -->
    <div class="modal fade job-drawer-modal" id="jobDrawerModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-slideout">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="drawer-header ">
                  <div class="d-flex justify-content-between align-items-center">
                     <h5 class="mb-0">Canberra General Hospital</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                  </div>
                    <h6 class="font-weight-bold">ICU Nurse — ICU</h6>

                    <!-- STATUS -->
                    <div class="job-status d-flex align-items-center gap-3">
                        <span class="active-step"><span class="active-circle"></span> Accepted</span>
                        <span><span class="active-circle"></span> Onboarding</span>
                        <span><span class="active-circle"></span>  Active</span>
                        <span><span class="active-circle"></span> Completed</span>
                    </div>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- JOB OVERVIEW -->
                    <div class="drawer-card">
                        <h6 class="text-14 font-weight-bold">Job Overview</h6>
                        <p class="text-12"><strong>Facility:</strong> Canberra General Hospital</p>
                        <p class="text-12"><strong>Start Date:</strong> Thu, 18 Apr</p>
                        <p class="text-12"><strong>Rate:</strong> $65/hr</p>
                        <hr class="my-1">
                        <p class="text-12"><i class="fa fa-map-marker mr-1" aria-hidden="true"></i>
                          <span class="ml-1">facility/Employer</span>
                        </p>
                         <hr class="my-1">
                         <div class="text-12">
                           <p>Start Date:</p>
                         </div>
                    </div>

                    <!-- DOCUMENTS -->
                    <div class="drawer-card">
                        <h6 class="text-14">Onboarding Documents</h6>

                        <div class="doc-row text-12 d-flex justify-content-between align-items-center">
                            AHPRA License
                            <span class="badge badge-warning">Pending</span>
                        </div>

                        <div class="doc-row text-12 d-flex justify-content-between align-items-center">
                            Police Check
                            <span class="badge badge-success">Verified</span>
                        </div>

                        <div class="doc-row text-12 d-flex justify-content-between align-items-center cursor-pointer">
                            Immunization
                            <span class="badge status-submitted open-job-drawer">
                                Upload
                            </span>
                        </div>
                    </div>
                    <div class="drawer-card">
                      <h6 class="text-14">Schedule / Timesheet</h6>
                      <div class="d-flex gap-2 justify-content-between">
                        <a href="#" class="text-primary text-12">View Calender</a>
                        <div>
                          <p class="text-12">Worked Hours:32h</p>
                          <a href="#" class="text-primary text-12">Pending Timesheet</a>
                        </div>
                      </div>
                    </div>
                    <div class="drawer-card">
                      <h6 class="text-14">Communications</h6>
                      <div class="d-flex gap-2 align-items-center mt-2">
                        <img src="" class="msg-dimension">
                        <div>
                        <div class="d-flex gap-2">
                          <p class="text-12 font-weight-bold">Sally Field</p>
                          <p class="text-12">HR Manager</p>
                        </div>
                         <div class="d-flex gap-2">
                          <p class="text-12">sally.field@canberra.au</p>
                          <p class="text-12">+61 25555 3333</p>
                        </div>
                        </div>
                        
                      </div>
                      
                    </div>



                </div>

                <!-- FOOTER ACTIONS -->
                <div class="drawer-footer">
                    <button class="btn btn-sm text-12 status-submitted text-white">Start Onboarding</button>
                    <button class="btn btn-sm btn-outline-secondary text-12">Message Facility</button>
                    <button class="btn btn-sm btn-danger text-12">Cancel Job</button>
                </div>

            </div>
        </div>
    </div>

<!-- ----- -->

<!-- ----- -->
@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
<script src="{{ url('/public') }}/nurse/assets/js/jquery.ui.datepicker.monthyearpicker.js"></script>
{{-- @include('nurse.front_profile_js'); --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script>
  $(document).on('click', '.active-status-modal', function () {
    let applicationId = $(this).data('id');
    $('#modalLoader').show();
    $('#modalTitle, #modalFacility, #modalContent, #modalFooter,#modalStatusLabel').html('');
    $.ajax({
        url: "{{ url('/nurse/application-timeline') }}",
        type: "GET",
        data: { application_id: applicationId },
        success: function (res) {
          console.log(res);
            $('#modalLoader').hide();
            $('#modalTitle').text(res.job_title);
            $('#modalFacility').text(res.facility);
            if (res.status) {
                $('#modalStatus')
                    .removeClass('d-none alert-primary alert-warning alert-info alert-success alert-secondary')
                    .addClass('alert alert-' + res.status.class);
                $('#modalStatusLabel').text(res.status.label);
                $('#modalStatusDesc').text(res.status.desc);
            }
            /* Timeline */
            let timeline_html = '';
            res.timeline.forEach(item => {
                timeline_html += `
                    <div class="timeline-item">
                        <strong>${item.title}</strong>
                        <p>${item.desc}</p>
                        <small>${item.date}</small>
                    </div>
                `;
            });
            $('#modalContent').html(timeline_html);
            /* Footer Action */
            if (res.action) {
                $('#modalFooter').html(`
                    <button 
                        class="btn ${res.action.class} btn-block modal-action-btn"
                        data-action="${res.action.type}"
                        data-id="${applicationId}">
                        ${res.action.label}
                    </button>
                `);
            }
        }
    });
  });
  $(document).on('click', '.modal-action-btn', function () {
      let action = $(this).data('action');
      let applicationId = $(this).data('id');
      if (action === 'withdraw') {
          if (!confirm('Are you sure you want to withdraw this application?')) return;
          // AJAX → withdraw endpoint
          console.log('Withdraw', applicationId);
      }
      if (action === 'interview') {
          window.location.href = `/nurse/interview/${applicationId}`;
      }
      if (action === 'offer_view') {
          window.location.href = `/nurse/offer/${applicationId}`;
      }
      if (action === 'offer_accept') {
          window.location.href = `/nurse/offer/${applicationId}/accept`;
      }
  });
</script>
<script>
      $(document).on('click', '.view-interview-details', function () {

        $('#modalContainer').empty();
        let applicationId = $(this).data('id');

        $.ajax({
            url: "{{ url('/nurse/action-application') }}",
            type: "GET",
            data: { application_id: applicationId ,modal_no:"1"},
            success: function (response) {

                $('#modalContainer').empty();   // 🔥 prevent stacking
                $('#modalContainer').html(response);

                $('#interviewProcessModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#interviewProcessModal').modal('show');
            }
        });
    });

    $(document).on('hidden.bs.modal', '#interviewProcessModal', function () {
        $(this).remove();
    });

</script>
<script>
      $(document).on('click', '.view-offer', function () {

        let applicationId = $(this).data('id');

        $.ajax({
            url: "{{ url('/nurse/action-application') }}",
            type: "GET",
            data: { application_id: applicationId ,modal_no:"2" },
            success: function (response) {

                $('#modalContainer').empty(); 
                $('#modalContainer').html(response);

                $('#offerReviewModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#offerReviewModal').modal('show');
            }
        });
    });

    $(document).on('hidden.bs.modal', '#offerReviewModal', function () {
        $(this).remove();
    });

</script>
<script>
      $(document).on('click', '.accept-offer', function () {

        let applicationId = $(this).data('id');

        $.ajax({
            url: "{{ url('/nurse/action-application') }}",
            type: "GET",
            data: { application_id: applicationId ,modal_no:"3" },
            success: function (response) {

                $('#modalContainer').empty(); 
                $('#modalContainer').html(response);

                $('#acceptOfferModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#acceptOfferModal').modal('show');
            }
        });
    });

    $(document).on('hidden.bs.modal', '#acceptOfferModal', function () {
        $(this).remove();
    });
</script>
<script>
      $(document).on('click', '.active-withdrawModal', function () {

        let applicationId = $(this).data('id');

        $.ajax({
            url: "{{ url('/nurse/action-application') }}",
            type: "GET",
            data: { application_id: applicationId ,modal_no:"4" },
            success: function (response) {

                $('#modalContainer').empty(); 
                $('#modalContainer').html(response);

                $('#withdrawModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#withdrawModal').modal('show');
            }
        });
    });

    $(document).on('hidden.bs.modal', '#withdrawModal', function () {
        $(this).remove();
    });
</script>
<script>
      $(document).on('click', '.archieved-withdrawn', function () {

        let applicationId = $(this).data('id');

        $.ajax({
            url: "{{ url('/nurse/action-application') }}",
            type: "GET",
            data: { application_id: applicationId ,modal_no:"5" },
            success: function (response) {

                $('#modalContainer').empty(); 
                $('#modalContainer').html(response);

                $('#withdrawnStatusModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#withdrawnStatusModal').modal('show');
            }
        });
    });

    $(document).on('hidden.bs.modal', '#withdrawnStatusModal', function () {
        $(this).remove();
    });
</script>
<script>
      $(document).on('click', '.archieved-rejected', function () {

        let applicationId = $(this).data('id');

        $.ajax({
            url: "{{ url('/nurse/action-application') }}",
            type: "GET",
            data: { application_id: applicationId ,modal_no:"6" },
            success: function (response) {

                $('#modalContainer').empty(); 
                $('#modalContainer').html(response);

                $('#rejectedModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#rejectedModal').modal('show');
            }
        });
    });

    $(document).on('hidden.bs.modal', '#rejectedModal', function () {
        $(this).remove();
    });
</script>
<script>
      $(document).on('click', '.archieved-hireed', function () {

        let applicationId = $(this).data('id');

        $.ajax({
            url: "{{ url('/nurse/action-application') }}",
            type: "GET",
            data: { application_id: applicationId ,modal_no:"7" },
            success: function (response) {

                $('#modalContainer').empty(); 
                $('#modalContainer').html(response);

                $('#hiredModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#hiredModal').modal('show');
            }
        });
    });

    $(document).on('hidden.bs.modal', '#hiredModal', function () {
        $(this).remove();
    });
</script>
 <script>
        $(document).ready(function () {

            $(".open-job-drawer").on("click", function () {
                $("#jobDrawerModal").modal("show");
            });

        });
    </script>

@endsection