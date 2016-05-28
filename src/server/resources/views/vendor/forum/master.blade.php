<!doctype html>
<html lang="pt-pt">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if (isset($thread))
            {{ $thread->title }} -
        @endif
        @if (isset($category))
            {{ $category->title }} -
        @endif
        {{ trans('forum::general.home_title') }}
    </title>
  <link rel="stylesheet" href="/css/style.css">
  <style>
    ol.breadcrumb {
      margin-top: 24px;
    }
.background-primary ol a, .panel-heading a {
    color: green !important;
}


    textarea {
        min-height: 200px;
    }

  </style>
</head>
<body id="">
<div class="container-fluid">
    <div class="row">
      <div class="col-md-12 envWarning text-center text-danger smt smb">
     Site running in test mode. Inserted data will be destroyed regularly.
      </div>
    </div>
</div>
<header class="nav navbar-static-top" role="banner">
  <div class="container">
    <nav class="collapse navbar-collapse">
      <ul class="nav nav-pills navbar-nav navbar-right">
        <?php $menu = \Lang::get('seedbank::menu'); ?>
        <li role="presentation"><a href="/seedbank"><?php echo $menu['home']; ?></a></li>
        <li role="presentation"><a href="/seedbank/myseeds"><?php echo $menu['myseeds']; ?></a></li>
        <li role="presentation"><a href="/seedbank/exchanges"><?php echo $menu['exchanges']; ?></a></li>
        <li role="presentation"><a href="/seedbank/search"><?php echo $menu['search']; ?></a></li>
        <li role="presentation"><a href="/events"><?php echo $menu['events']; ?></a></li>
        <li role="presentation" class="active"><a href="/forum"><?php echo $menu['forum']; ?></a></li>
        <li role="presentation"><a href="/sementecas"><?php echo $menu['seedshare']; ?></a></li>
<?php $user = \Auth::user();
 if ( $user->is_admin() ) { $useradmin = true; } ?>
        @if (isset($useradmin))
        <li class="dropdown" role="presentation">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $menu['admin']; ?><span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li role="presentation"><a href="/events"><?php echo $menu['events']; ?></a></li>
            <li role="presentation"><a href="/sementecas"><?php echo $menu['seedshare']; ?></a></li>
          </ul>
        </li>
        @endif
        <li class="dropdown" role="presentation">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user->name ?><span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/seedbank/preferences"><?php echo $menu['profile']; ?></a></li>
            <li role="separator" class="divider"></li>
            <li role="presentation"><a href="/en/auth/logout"><?php echo $menu['logout']; ?></a></li>
          </ul>
        </li>
      </ul>
      <!--<ul class="nav nav-pills navbar-nav">
      </ul>-->
    </nav>
  </div>
</header>


<div class="pageWrap background-primary">
    <div class="container">
<div class="col-md-12 text-center">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
</div>
    </div>
</div>

<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
    <script>
    var toggle = $('input[type=checkbox][data-toggle-all]');
    var checkboxes = $('table tbody input[type=checkbox]');
    var actions = $('[data-actions]');
    var forms = $('[data-actions-form]');
    var confirmString = "{{ trans('forum::general.generic_confirm') }}";

    function setToggleStates() {
        checkboxes.prop('checked', toggle.is(':checked')).change();
    }

    function setSelectionStates() {
        checkboxes.each(function() {
            var tr = $(this).parents('tr');

            $(this).is(':checked') ? tr.addClass('active') : tr.removeClass('active');

            checkboxes.filter(':checked').length ? $('[data-bulk-actions]').removeClass('hidden') : $('[data-bulk-actions]').addClass('hidden');
        });
    }

    function setActionStates() {
        forms.each(function() {
            var form = $(this);
            var method = form.find('input[name=_method]');
            var selected = form.find('select[name=action] option:selected');
            var depends = form.find('[data-depends]');

            selected.each(function() {
                if ($(this).attr('data-method')) {
                    method.val($(this).data('method'));
                } else {
                    method.val('patch');
                }
            });

            depends.each(function() {
                (selected.val() == $(this).data('depends')) ? $(this).removeClass('hidden') : $(this).addClass('hidden');
            });
        });
    }

    setToggleStates();
    setSelectionStates();
    setActionStates();

    toggle.click(setToggleStates);
    checkboxes.change(setSelectionStates);
    actions.change(setActionStates);

    forms.submit(function() {
        var action = $(this).find('[data-actions]').find(':selected');

        if (action.is('[data-confirm]')) {
            return confirm(confirmString);
        }

        return true;
    });

    $('form[data-confirm]').submit(function() {
        return confirm(confirmString);
    });
    </script>

    @yield('footer')
</body>
</html>
