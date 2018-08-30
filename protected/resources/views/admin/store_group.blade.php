@extends('layouts.admin')

@push('css')
@endpush
{{-- ================================================================================================================================= --}}
@push('sec-js')
<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/legacy.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
<script type="text/javascript" src="{{ asset('js/mixin.'.auth()->user()->language.'.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.store_group'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div class="row">
	<div class="col-md-2 col-md-offset-3">
		<div class="panel panel-flat">
			<ul class="media-list media-list-linked">
				@foreach (App\StoreGroup::where('code', '!=', 2712)->get() as $group)
				<li class="media" @click="editData( {{ $group }} )">
					<a href="#" class="media-link">
						<div class="media-body">
							<div class="media-heading text-semibold">{{ $group->name }}</div>
						</div>
					</a>
				</li>
				<hr style="margin:0px;">
				@endforeach
			</ul>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<form class="form-horizontal" :action="actionUrl" method="post" autocomplete="off">
					<fieldset class="content-group">
						<legend class="text-bold" v-if="!editStatus">@lang('trans.add_group')</legend>
						<legend class="text-bold" v-if="editStatus">
							<i class="icon-circle-left2 position-left" @click="addData()" style="cursor: pointer;"></i>@lang('trans.edit_group')
						</legend>
						{{csrf_field()}}
						<input type="hidden" name="_method" value="PUT" v-if="editStatus">
						<input type="hidden" name="id" :value="group.id" v-if="editStatus">
						<div class="form-group">
							<div class="col-lg-5">
								<label class="control-label col-lg-12">@lang('trans.group_code')</label>
							</div>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="code" :value="group.code">
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-5">
								<label class="control-label col-lg-12">@lang('trans.group_name')</label>
							</div>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="name" :value="group.name">
							</div>
						</div>
					</fieldset>
					<div class="text-right">
						<button type="submit" class="btn btn-primary btn-xs">@lang('trans.save')</button>
						<button type="submit" class="btn btn-danger btn-xs pull-left" v-if="editStatus" @click="deleteData(event, {{ $group->id }} )">@lang('trans.delete')</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var controller = new Vue({
		el: '#controller',
		data : {
			group: {},
			editStatus: false,
			actionUrl: URL+'data/group'
		},
		mounted:function() {

		},
		methods: {
			addData() {
				this.actionUrl = URL+'data/group';
				this.editStatus = false;
				this.group = {}
			},
			editData(group) {
				this.actionUrl = URL+'data/group/'+group.id;
				this.editStatus = true;
				this.group = group;
			},
			deleteData(event, id) {
				// alert(id)
				if (confirm(translate("are_you_sure"))) {
					axios.post(URL+'data/group/'+id,{_method:'DELETE'}).then(function(){
						location.reload();
					});
				}
			}
		}
	});
</script>
@endpush