@extends('emails.app.layouts.master-en')

@section('content')

    <p class="lead">Hello {{ $name }},</p>
    <p>Thank you for signing up.</p>
    <p>Please click the link below to activate your account.</p>

    <p class="callout">
<div><!--[if mso]>
  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:53px;v-text-anchor:middle;width:200px;" arcsize="8%" stroke="f" fillcolor="#49a9ce">
    <w:anchorlock/>
    <center>
  <![endif]-->
      <a href="{{ $link }}"
style="background-color:#49a9ce;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:53px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;">
Activate my account</a>
  <!--[if mso]>
    </center>
  </v:roundrect>
<![endif]--></div>
    </p>

@stop