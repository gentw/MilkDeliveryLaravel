@extends('naizhan.layout.master')

@section('css')
@endsection

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('naizhan/dingdan')}}">订单管理</a>
				</li>
				<li class="active">
					<strong>续单列表</strong>
				</li>
			</ol>
		</div>
		<div class="row wrapper">
			<div class="wrapper-content">

				<!-- 筛选选择项 -->
				@include('gongchang.dingdan.orderlistfilter', [
                    'showState' => false,
                    'showEndDate' => false,
                ])

				<div class="feed-element">
					<div class="col-md-2 col-md-offset-10 button-div">
						<button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
						<button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
					</div>
				</div>

				<div class="ibox float-e-margins white-bg">
					<div class="ibox-content">
						<table id="order_table" class="table table-bordered footable" data-sort-ignore="true" >
							<thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">订单号</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">地址</th>
									<th data-sort-ignore="true">订单类型</th>
									<th data-sort-ignore="true">订单金额</th>
									<th data-sort-ignore="true">征订员</th>
									<th data-sort-ignore="true">区域</th>
									<th data-sort-ignore="true">分区</th>
									<th data-sort-ignore="true">奶站</th>
									<th data-sort-ignore="true">配送员</th>
									<th data-sort-ignore="true">下单日期</th>
									<th data-sort-ignore="true">支付</th>
									<th data-sort-ignore="true">订单性质</th>
									<th data-sort-ignore="true">状态</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
							</thead>
							<tbody>
								@if (isset($orders))
									@for($i =0; $i<count($orders); $i++)
										<tr data-orderid="{{$orders[$i]->id}}" class="row-hover-light-blue">
											<td>{{$i + $orders->firstItem()}}</td>
											<td class="o_number">{{$orders[$i]->number}}</td>
											<td class="o_customer_name">{{$orders[$i]->customer_name}}</td>
											<td class="o_phone">{{$orders[$i]->phone}}</td>
											<td class="o_addr">{{$orders[$i]->address}}</td>
											<td class="o_type">{{$orders[$i]->all_order_types}}</td>
											<td class="o_total">{{$orders[$i]->total_amount}}</td>
											<td class="o_checker">{{$orders[$i]->getCheckerName()}}</td>
											<td class="o_city">{{$orders[$i]->getCityName()}}</td>
											<td class="o_street">{{$orders[$i]->getDistrictName()}}</td>
											<td class="o_station">{{$orders[$i]->delivery_station_name}}</td>
											<td class="milkman">{{$orders[$i]->milkman_name}}</td>
											<td class="o_ordered">{{$orders[$i]->ordered_at}}</td>
											<td class="o_paytype">{{$orders[$i]->payment_type_name}}</td>
											<td class="o_property">{{$orders[$i]->getOrderPropertyName()}}</td>
											<td class="o_status" data-status="{{$orders[$i]->status}}" data-value="{{$orders[$i]->status_name}}" style="width: 70px;">
												{{$orders[$i]->status_name}}</td>
											<td>{{$orders[$i]->comment}}</td>
										</tr>
									@endfor
								@endif

							</tbody>
						</table>

						<ul id="pagination_data" class="pagination-sm pull-right"></ul>

					</div>
				</div>
			</div>
		</div>

	</div>
@endsection

<!-- script -->
@include('gongchang.dingdan.orderscript', [
	'pageName' => 'xudan',
    'isStation' => true,
])
