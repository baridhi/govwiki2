<?php
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
  <head>
    <!--include head.jade-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-9">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://californiapolicycenter.org/wp-content/uploads/sites/2/2015/04/favicon_v1.png" type="image/png">
    <title>CPC Civic Profiles</title>
    <!-- Bootstrap core CSS-->
    <link href="/legacy/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this page-->
    <link rel="stylesheet" href="/legacy/css/index.css">
    <!--script(src='js_files/ie-emulation-modes-warning.js')-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries-->
    <!--if lt IE 9
    script(src='https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js')
    |
    script(src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js')

    -->
  </head>
  <body>
  <script>
    window.path = "{$app->getRequest()->getPathInfo()}";
  </script>
    <div style="padding:0" class="container">
      <div id="header">
        <div class="container">
          <div class="innder-container"><strong class="logo"><a href="http://californiapolicycenter.org/"><img src="/legacy/img/logo_cpc_v2_244x60.png" alt="California Policy Center"></a></strong>
            <ul class="nav navbar-nav navbar-right search-icon">
              <li id="searchIcon"><a href="javascript:void(0);" onclick="GOVWIKI.history(0)">Return to Map &nbsp;&nbsp;&nbsp;<span style="font-size:200%;vertical-align:middle;"></span></a></li>
              <li id="stantonIcon"><a href="javascript:void(0);" onclick="GOVWIKI.history(-1)">Return to Stanton &nbsp;&nbsp;&nbsp;<span style="font-size:200%;vertical-align:middle;"></span></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- Search & Filters-->
    <div id="searchContainer" style="width0:100%" class="container container-fluid"></div>
    <!-- DATA-->
    <div id="dataContainer" class="container">
      <!--p#pBackToSearch.text-center
      a#btnBackToSearch.btn.btn-default(href='', role='button') back to search&nbsp;&nbsp;
        span.glyphicon.glyphicon-search
      -->
      <div class="row">
        <div class="col-lg-12">
          <div class="loader"></div>
          <div id="details"></div>
          <div id="wikipediaContainer">wikipedia container</div>
        </div>
      </div>
    </div>
    <div style="padding:0" class="container">
      <footer class="container_wrap socket_color" id="socket" role="contentinfo" itemscope="itemscope" itemtype="https://schema.org/WPFooter">
      <div class="container">
      <span class="copyright">The California Policy Center is a 501c3 non-profit public charity. CA Corp. # 3295222. Federal EIN 27-2870463.<br>Copyright © California Policy Center 2015. All rights reserved</span>
      <ul class="noLightbox social_bookmarks icon_count_3"><li class="social_bookmarks_facebook av-social-link-facebook social_icon_1"><a target="_blank" href="https://www.facebook.com/CalPolicyCenter" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello" title="Facebook"><span class="avia_hidden_link_text">Facebook</span></a></li><li class="social_bookmarks_twitter av-social-link-twitter social_icon_2"><a target="_blank" href="https://twitter.com/calpolicycenter" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello" title="Twitter"><span class="avia_hidden_link_text">Twitter</span></a></li><li class="social_bookmarks_linkedin av-social-link-linkedin social_icon_3"><a target="_blank" href="https://www.linkedin.com/company/california-policy-center" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello" title="Linkedin"><span class="avia_hidden_link_text">Linkedin</span></a></li></ul><nav class="sub_menu_socket" role="navigation" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"><div class="avia3-menu"><ul id="avia3-menu" class="menu"><li id="menu-item-4677" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-4677"><a href="http://californiapolicycenter.org">HOME</a></li>
      <li id="menu-item-4666" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-4666"><a href="http://californiapolicycenter.org/contact/">CONTACT US</a></li>
      </ul></div></nav>
      </div>
      </footer>
    </div><script id="tab-template" type="text/x-handlebars-template">
    <li role="presentation" class="{{active}}">
        <a href="#{{tabid}}" aria-controls="home" role="tab"
          data-toggle="tab" data-tabname="{{tabname}}">{{tabname}}</a>
    </li>
</script>

<script id="tabpanel-template" type="text/x-handlebars-template">
    <div id="details-inner">
    <div style="min-height:60px;">
      <div style="text-align:right; min-height:48px;" class="pull-right top-links-wrapper">
        {{#if wikipedia_page_name}}
            <a  target="_blank" href="{{wikipedia_page_name}}">
              Open Wikipedia article
              <img src="http://www.mailamengg.com/Portals/0/photo1/logo/wikipedia.png" style="width:36px; height:36px">
            </a>
        {{/if}}
        {{#if latest_audit_url}}
            <a target="_blank" href="{{latest_audit_url}}" style="margin-left:20px;">
              Latest Audit
              <img src="data:image/png;base64,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" style="">
            </a>
        {{/if}}
        {{#if transparent_california_page_name}}
            <a  target="_blank" href="{{transparent_california_page_name}}" style="margin-left:20px;">
              Transparent California
              <img src="http://us.cdn2.123rf.com/168nwm/blankstock/blankstock1409/blankstock140900218/31369868-text-edit-sign-icon-letter-t-button-circle-flat-button-with-shadow-modern-ui-website-navigation-vect.jpg" style="width:36px; height:36px">
            </a>
        {{/if}}
      </div>
      <h3 class="county-title" style="text-transform:uppercase;padding:0; margin:0;">{{{title}}}</h3>
    </div>
    <div class="tabpanel">
            <ul id="fieldTabs" class="nav nav-pills" role="tablist">
                {{#each tabs}}
                    {{> tab-template }}
                {{/each}}
            </ul>
            <div id="tabsContent" class="tab-content">
                {{{tabcontent}}}
            </div>
        </div>

    <br>
    <br>
    <br>
    <br>
    <div id="wiki" style="display:none0">
        {{#if wikipedia_page_name}}
            <a  href="javascript:get_wikipedia_article('{{wikipedia_page_name}}')">
              Show Wikipedia article
            </a>
            <h3 id="wikipediaTitle"></h3>
            <div id="wikipediaArticle"></div>
            {{else}}
            There is no corresponding article at Wikipedia site. You can create a new one.<br>
            Title:<br>
            <input id="newWikipediaTitle" type="text" value="{{gov_name}}" style="width:100%">
            <br>
            Text:<br>
            <textarea id="newWikipediaArticle" style="width:100%; height:200px;border:1px solid #DDD">
            {{gov_name}}
            </textarea>
            <button class="btn btn-primary pull-right" type="button" onclick="create_wikipedia_article()">
                Create Wikipedia article
            </button>
        {{/if}}
    </div>
    </div>
</script>

<script id="tabdetail-template" type="text/x-handlebars-template">
    <div role="tabpanel" class="tab-pane one-tab {{active}}" id="{{tabid}}" style="padding-top: 20px">
        <h4>{{tabname}}</h4>
        <br />
        {{{tabcontent}}}
    </div>
</script>

<script id="tabdetail-namevalue-template" type="text/x-handlebars-template">
    <div>
            <span class="f-nam" >{{name}}
                {{#if help}}
                <div style='display:inline;color:#074d71' title='{{help}}'><img src="/legacy/img/678110-sign-info-16.png" /></div>
                {{/if}}
            </span>
            <span class="f-val">{{{value}}}</span>
    </div>
</script>

<script id="tabdetail-finstatement-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-sm-5">{{{name}}}</div>
        <div class="col-sm-2 text-right" data-col="1">{{{genfund}}}</div>
        <div class="col-sm-2 text-right" data-col="2">{{{otherfunds}}}</div>
        <div class="col-sm-2 text-right" data-col="3">{{{totalfunds}}}</div>
    </div>
</script>

<script id="tabdetail-official-template" type="text/x-handlebars-template">
    <div style="margin-top:20px;" class="f-names">
        <span class="f-nam f-nam-margin">
            {{title}}<br>
            <a href="/{{altTypeSlug}}/{{nameSlug}}/{{slug}}" class="elected_link">{{name}}</a><br>
            {{#if email}}
            <a href="mailto:{{email}}">{{email}}</a><br />
            {{/if}}
            {{#if telephonenumber}}
            {{telephonenumber}}<br />
            {{/if}}
            {{termexpires}}
        </span>
        {{#if image}}
        <a href="/{{altTypeSlug}}/{{nameSlug}}/{{slug}}" class="elected_link"><span class="f-val">{{{image}}}</span></a>
        {{/if}}
    </div>
</script>

<script id="tabdetail-employee-comp-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
            {{{ content }}}
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <div id="median-comp-graph" style="height: 16em;"></div>
            <div id="median-pension-graph" style="height: 16em;"></div>
            <div id="pct-pension-graph" style="height: 16em;"></div>
        </div>
    </div>
</script>

<script id="tabdetail-financial-health-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
            {{{ content }}}
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <div id="public-safety-pie" style="height: 16em;"></div>
            <div id="fin-health-revenue-graph" style="height: 17em;"></div>
            <div id="fin-health-expenditures-graph" style="height: 17em;"></div>
        </div>
    </div>
</script>

<script id="tabdetail-financial-statements-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div id="total-revenue-pie" style="height: 16em;margin-bottom: 17px;"></div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div id="total-expenditures-pie" style="height: 16em;"></div>
        </div>
        <div class="col-sm-12 fin-values-block">
            {{{ content }}}
        </div>
    </div>
</script>

<script id="person-info-template" type="text/x-handlebars-template">

    <div class="row person-info-header">
        <div class="col-md-10 col-md-push-1">
            <h5>{{gov_name}}</h5>
            <h5>ELECTED OFFICIALS - PROFILE</h5>
        </div>
    </div>

    <div class="row person-info-content">
        <div class="col-xs-7 col-sm-7 col-md-7 col-md-push-1 col-lg-7 col-lg-push-1" style="font-size:16px;">
            <p>Title: {{title}}</p>
            <p>Name: {{full_name}} &nbsp;&nbsp;
                <span class="disqus-comment-count vote" id="{{elected_official_id}}" data-disqus-identifier="{{elected_official_id}}">0</span>
            <p>Term expiries: {{term_expires}}</p>
            {{#if bio_url}}
                <p><a target="_blank" href="{{bio_url}}">Biographical Highlights</a></p>
            {{/if}}
            <p>Email: <a href="mailto:{{email_address}}">{{email_address}}</a></p>
        </div>

        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <img src="{{photo_url}}" class="person-photo"/>
        </div>
    </div>

    <div class="row person-info-tabs">
        <div class="col-md-10 col-md-push-1">

            <!-- Nav tabs -->
            <ul class="nav nav-pills" role="tablist">
                <li role="presentation" class="active"><a href="#Votes" aria-controls="Votes" role="tab" data-toggle="tab">Votes</a></li>
                <li role="presentation"><a href="#Contributions" aria-controls="Contributions" role="tab" data-toggle="tab">Contributions</a></li>
                <li role="presentation"><a href="#Endorsements" aria-controls="Endorsements" role="tab" data-toggle="tab">Endorsements</a></li>
                <li role="presentation"><a href="#Statements" aria-controls="Statements" role="tab" data-toggle="tab">Public Statements</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content" style="margin-top: 40px; margin-bottom: 40px;">

                <div role="tabpanel" class="tab-pane active" id="Votes">
                    {{#if votes}}
                        <table class="table table-hover">
                            <tr>
                                <th>Date</th>
                                <th>Title of Measure</th>
                                <th>Summary of Measure</th>
                                <th style="text-align:center;">How official Voted</th>
                                <th style="text-align:center;">Sponsored by this official?</th>
                                <th>Category</th>
                                <th></th>
                            </tr>

                            {{#votes}}
                                <tr>
                                {{#this}}
                                    <td>{{legislation.date_considered}}</td>
                                    <td>{{legislation.name}}</td>
                                    <td>{{legislation.summary}}</td>
                                    <td align="center">{{vote}}</td>
                                    <td align="center">{{#if_eq did_elected_official_propose_this true}} Yes {{else}} No {{/if_eq}}</td>
                                    <td>{{legislation.issue_category.name}}</td>
                                    <td><span class="disqus-comment-count vote" id="{{../../id}}_v{{id}}" data-legislation-name="{{legislation.name}}" data-disqus-identifier="{{../../id}}_v{{id}}">0</span></td>
                                {{/this}}
                                </tr>
                            {{/votes}}
                        </table>
                    {{else}}
                        <p style="font-size:18px;">No information at this time. Please check back later.</p>
                    {{/if}}
                </div>

                <div role="tabpanel" class="tab-pane" id="Contributions">
                    {{#if contributions}}
                        <table class="table table-hover">
                            <tr>
                                <th>Election Year</th>
                                <th>Name of contributor</th>
                                <th>Ind. Exp. Desc.</th>
                                <th></th>
                                <th>Amount</th>
                                <th>Contributor Type</th>
                            </tr>

                            {{#contributions}}
                            <tr>
                                {{#this}}
                                    <td>{{election_year}}</td>
                                    <td>{{contributor_name}}</td>
                                    <td>{{independent_expenditure_desc}}</td>
                                    <td>{{#unless @index}}\x36{{/unless}}</td>
                                    <td>{{contribution_amount}}</td>
                                    <td>{{contributor_type}}</td>
                                {{/this}}
                            </tr>
                            {{/contributions}}

                        </table>
                    {{else}}
                        <p style="font-size:18px;">No information at this time. Please check back later.</p>
                    {{/if}}
                </div>

                <div role="tabpanel" class="tab-pane" id="Endorsements">
                    {{#if endorsements}}
                        <table class="table table-hover">
                            <tr>
                                <th>Election Year</th>
                                <th>Name</th>
                                <th>Type</th>
                            </tr>
                            {{#endorsements}}
                            <tr>
                                {{#this}}
                                    <td>{{election_year}}</td>
                                    <td>{{name_of_endorser}}</td>
                                    <td>{{endorser_type}}</td>
                                {{/this}}
                            </tr>
                            {{/endorsements}}
                        </table>
                    {{else}}
                        <p style="font-size:18px;">No information at this time. Please check back later.</p>
                    {{/if}}
                </div>

                <div role="tabpanel" class="tab-pane" id="Statements">
                    <p style="font-size:18px;">No information at this time. Please check back later.</p>
                </div>
            </div>

        </div>
    </div>

</script>

<script id="search-container-template" type="text/x-handlebars-template">
    <div style="padding-top:40px;margin-bottom:10px;" class="row search-form">
        <div id="typeahed-container" style="border:0px solid red" class="col-sm-5">
            <input id="myinput" type="text" placeholder="Loading the list of 91,000 US governments..."
                   style="width:412px" class="text-uppercase typeahead form-control0">

            <p class="text-center text-nowrap hidden-xs hidden-sm">Type part of the agency's name &nbsp;&nbsp;<img
                src="/legacy/img/FullSizeRender.gif" style="width:40px">
                <!--span(style="font-size:150%; position:relative; top:-6px;")⤴-->&nbsp; or click it on the map &nbsp;&nbsp;<img
                    src="/legacy/img/FullSizeRender1.gif" style="width:40px">
                <!--span(style="font-size:150%;position: relative; top:12px;")⤵-->
            </p>
        </div>
        <div style="border:0px solid red; opacity:0.5;padding:0;margin:0; line-height:1"
             class="col-sm-2 text-center hidden-xs">
            <div style="margin-top:8px; font-size:150%;font-weight:bold;" class="gov-counter"></div>
        </div>
    </div>
    <div style="margin-bottom:100px;" class="row">
        <div style="padding:0;" class="col-sm-12 col-lg-5 col-md-6">
            <div id="maparea" style="border:1px solid #e1e1e1;margin-left:10px;">
                <div id="govmap"></div>
                <!-- Controls to show results of geocoding-->
                <!--div
                .input-group
                  span.input-group-addon
                    strong FIND:
                  input#govaddress.form-control.input-sm ss
                  .input-group-btn
                    button.glyphicon.glyphicon-search.btn.btn-default.input-sm(onclick="geocode_addr('#govaddress')")
                .govmap-found
                -->
            </div>
        </div>
        <div class="col-lg-7 col-md-6 col-sm-12">
            <div id="intro-text"></div>
            <!--include right_panel.html-->
        </div>
    </div>
    <div style="display:none;">
        <div id="legend" class="panel panel-default">
            <div class="panel-heading">Select type(s):</div>
            <ul class="list-group">
                <li class="list-group-item active"><span class="glyphicon glyphicon-ok"></span><i
                    class="red-circle marker-circle"></i>Cities
                    <input type="hidden" name="City" value="1" class="type_filter">
                </li>
                <li class="list-group-item active counties-trigger"><span class="glyphicon glyphicon-ok"></span><i
                    class="grey-line"></i><!--<span class="counties-title">-->Counties<!--</span>--></li>
                <li class="list-group-item active"><span class="glyphicon glyphicon-ok"></span><i
                    class="blue-circle marker-circle"></i>School Districts
                    <input type="hidden" name="School District" value="1" class="type_filter">
                </li>
                <li class="list-group-item active"><span class="glyphicon glyphicon-ok"></span><i
                    class="purple-circle marker-circle"></i>Special Districts
                    <input type="hidden" name="Special District" value="1" class="type_filter">
                </li>
            </ul>
        </div>
    </div>
</script>


<!-- Modal -->
<div class="modal fade" id="conversation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="disqus_thread"></div>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts -- Placed at the end of the document so the pages load faster-->
    <script src="/legacy/js_files/jquery.min.js"></script>
    <script src="/legacy/js/vendor/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug-->
    <script src="/legacy/js_files/ie10-viewport-bug-workaround.js"></script>
    <script src="/legacy/js/plugins.js"></script>
    <script src="/legacy/bower_components/flot/jquery.flot.js"></script>
    <script src="/legacy/js/vendor/jquery.flot.stack.patched.js"></script>
    <script src="/legacy/bower_components/flot/jquery.flot.pie.js"></script>
    <script src="/legacy/bower_components/numeral/numeral.js"></script>
    <script src="/legacy/bower_components/typeahead-0.10.5.js/dist/typeahead.bundle.min.js"></script>
    <script src="/legacy/bower_components/handlebars/handlebars.min.js"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script src="/legacy/bower_components/gmaps/gmaps.js"></script>
    <script src="http://www.google.com/jsapi"></script>
    <script src="/legacy/js/vendor/markerwithlabel_packed.js"></script>
    <script src="/legacy/js_files/disqus.js"></script>
    <script src="/legacy/static/bundle.min.js"></script>
    <script src="/legacy/js/vendor/history.js"></script>
  </body>
</html>
EOT;
?>
