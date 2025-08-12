{{-- Includable CSS --}}
@yield('styles')
<style media="screen">

html,
body {
  height: 100%;
  margin: 0px;
  padding: 0px;
  font-size: 13px;
  font-weight: 400;
  font-family: 'Nunito', sans-serif;
}

table {
  width: 100%;
  margin-bottom: 1rem;
  border-collapse: collapse;
}

th {
  text-align: inherit;
  text-align: -webkit-match-parent;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #EBEDF3;
}

.table thead th {
  vertical-align: bottom;
  border-bottom: 2px solid #EBEDF3;
}

.table-bordered {
  border: 1px solid #EBEDF3;
}

.table-bordered th,
.table-bordered td {
  border: 1px solid #EBEDF3;
}

.table-bordered thead th {
  border-bottom-width: 2px;
}

.text-center {
  text-align: center !important;
}

.text-right {
  text-align: right !important;
}

.font-size-h4 {
  font-size: 1.35rem !important;
}

.my-2 {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.mr-2 {
  margin-right: 0.5rem;
}
.page_break {
  page-break-before: always;
}

.bg-white {
  background-color: #fff;
}

footer {
    position: absolute; 
    bottom: 10; 
    left: 0px; 
    right: 0px;
    /* height: 50px;  */

    /** Extra personal styles **/
    /* background-color: #03a9f4;
    color: white; */
    text-align: center;
    line-height: 35px;
}

footer td {
  padding: 0;
  line-height: 1.5;
}

.text-muted {
  color: #70707c!important;
}

.pr-40 {
  padding-right: 40px;
}

.lh-2 {
  line-height: 2;
}

.inline {
  display: inline-block;
}

/* striped */
.table-striped tbody tr:nth-of-type(odd) {
  background-color: #EBEDF3;
}
.table-dark.table-striped tbody tr:nth-of-type(odd) {
  background-color: rgba(255, 255, 255, 0.05);
}
.table-striped tbody tr:nth-of-type(odd) {
  background-color: #EBEDF3;
}
/* striped */
/* labels */
.label {
    padding: 0;
    margin: 0;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    font-size: 0.8rem;
    background-color: #EBEDF3;
    color: #3F4254;
    font-weight: 400;
    height: 20px;
    width: 20px;
    font-size: 0.8rem;
}
.label.label-lg.label-inline {
    width: auto;
}
.font-weight-bold {
    font-weight: 500 !important;
}
.label.label-inline.label-lg {
    padding: 0.9rem 0.75rem;
}
.label.label-lg {
  height: 24px;
  width: 24px;
  font-size: 0.9rem;
}
.label.label-inline {
    width: auto;
}
.label.label-inline {
    width: auto;
    padding: 0.15rem 0.75rem;
    border-radius: 0.42rem;
}
.label.label-light-success {
    color: #1BC5BD;
    background-color: #C9F7F5;
}
.label.label-light-danger {
    color: #F64E60;
    background-color: #FFE2E5;
}
.fixlabel {
  display:block; width:60px; height:15px; text-align:center; margin: 0 auto;
}
/* labels */
.m-20 {
  margin: 20px;
}
.p-20 {
  padding: 20px;
}
</style>
