<template>
		<v-tooltip :content-class="`winvestify-tooltip ${small ? 'small' : ''}`" bottom>
			<i :class="{'show-icon': table, 'show-icon show-icon-small': tableSmall}" class="icon-win-info" slot="activator"></i>
			<slot name="content" />
		</v-tooltip>	
</template>
<script>
	export default {
		props: {
			small: {
				type: Boolean,
				default: false
			},
			table: {
				type: Boolean,
				default: false
			},
			tableSmall: {
				type: Boolean,
				default: false
			}
		}
	}
</script>