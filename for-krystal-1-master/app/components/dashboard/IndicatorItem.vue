<template>
	<v-flex class="item-indicator" xs12 sm4 md4 lg4>
		<div v-if="html" v-html="amount" class="amount"><i class="icon-win-airplay" v-if="amountIcon"> </i></div>
		<div v-else class="amount"><i class="icon-win-airplay" v-if="amountIcon"></i>{{ amount }}</div>
		<div class="title-indicator">
			<span>{{ name }}</span>
			<tooltip v-if="infoIcon" small>
				<template slot="content">
					<div>
						<p>{{ tooltipContent }}</p>
					</div>										
				</template>
			</tooltip>
		</div>
		<v-progress-linear :color="color" height="3" value="80"></v-progress-linear>
	</v-flex>
</template>
<script>
	import Tooltip from '~/components/ui/Tooltip'
	export default {
		props: {
			amount: {
				type: String,
				default: 0
			},
			color: {
				type: String,
				default: 'light-blue accent-3'
			},
			tooltipContent: {
				type: String,
				default: ''
			},
			name: {
				type: String,
				default: ''
			},
			amountIcon: {
				type: Boolean,
				default: false
			},
			infoIcon: {
				type: Boolean,
				default: false
			},
			showTitle: {
				type: Boolean,
				default: true
			},
			html: {
				type: Boolean,
				default: false
			}
		},
		components: {
			Tooltip
		}
	}
</script>