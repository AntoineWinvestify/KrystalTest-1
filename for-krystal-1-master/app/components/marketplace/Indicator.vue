<template>
	<div slot="activator" :class="{'indicator-left': left}" class="indicator-item">
		<template v-if="left" :class="{'title-left': left}" class="title-indicator">
			<tooltip v-if="data.tooltip_display_name" small>
				<template slot="content">
					<div>
						<p>{{ data.tooltip_display_name }}</p>
					</div>										
				</template>
			</tooltip>
			{{ data.display_name }}
		</template>
		<div class="indicator">
			<div :style="{ width: `${data.value}%` }" class="value"></div>
		</div>
		<template v-if="!left" class="title-indicator">
			{{ data.display_name }}
			<tooltip v-if="data.tooltip_display_name" tableSmall small>
				<template slot="content">
					<div>
						<p>{{ data.tooltip_display_name }}</p>
					</div>										
				</template>
			</tooltip>
		</template>
	</div>	
</template>
<script>
	import Tooltip from '~/components/ui/Tooltip'
	export default {
		props: {
			orientation: {
				type: String,
				default: 'vertical'
			},
			data: {
				type: Object,
				default: () => {
					return {}
				}
			},
			left: {
				type: Boolean,
				default: false
			}
		},
		components: {
			Tooltip
		}
	}
</script>