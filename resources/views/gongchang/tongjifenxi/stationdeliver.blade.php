@extends('gongchang.layout.master')
@section('css')
@endsection
@section('content')
	@include('gongchang.theme.sidebar')
	<div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">统计分析</a>
				</li>
				<li class="active"><strong>奶站配送统计</strong></li>
			</ol>
		</div>

		<div class="row">
			@include('gongchang.tongjifenxi.header', [
				'dateRange' => true,
			])

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="table1" class="table table-bordered">
						<thead>
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true" style="min-width:80px">奶站名称</th>
								@foreach($dates as $dt)
									<th data-sort-ignore="true">{{$dt}}</th>
								@endforeach
							</tr>
						</thead>
						<tbody>
						<?php $i = 0; ?>
						@foreach($stations as $st)
                            <?php $i++;?>
							<tr>
								<td>{{$i}}</td>
								<td>{{$st->name}}</td>
								@foreach($dates as $dt)
									<td>{{showEmptyValue(getEmptyArrayValue($counts, $st->id, $dt))}}</td>
								@endforeach
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
<script type="text/javascript">
    $('#data_range_select .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true
    });

    $('button[data-action = "print"]').click(function () {
        printContent('table1', gnUserTypeFactory, '奶站配送统计');
    });

    $('button[data-action = "export_csv"]').click(function () {
    data_export('table1', gnUserTypeStation, '奶站配送统计', 0, 1);
	});
</script>
@endsection