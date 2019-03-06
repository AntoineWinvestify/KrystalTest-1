<template>
	<div :class="{'horizontal': orientation==='horizontal', 'vertical': orientation==='vertical'}" class="thermometer-container">
		<div :class="{'horizontal': orientation==='horizontal', 'vertical': orientation==='vertical'}" class="thermometer" :style="{backgroundColor: mainColor}" >
			<div v-if="orientation==='vertical'" class="value" :style="{height: `${value}%`, backgroundColor: valueColor}"></div>
			<div v-else class="value" :style="{width: `${value}%`, backgroundColor: valueColor}"></div>
		</div>
		<div :class="{'horizontal': orientation==='horizontal', 'vertical': orientation==='vertical'}" class="thermometer-info" >
			<span>{{ value }}%</span>
			<span>{{ title }}</span>
		</div>
	</div>
</template>
<script>
	export default {
		props: {
			value: {
				type: Number,
				default: '0'
			},
			valueColor: {
				type: String,
				default: ''
			},
			mainColor: {
				type: String,
				default: ''
			},
			orientation: {
				type: String,
				default: 'vertical'
			},
			title: {
				type: String,
				default: ''
			}
		}
	}
</script>
<style lang="stylus" scoped>
.thermometer-container
	&.vertical
		margin-top: 2em
		display: flex
		flex-direction: column
		justify-content: center
		align-items: center
	&.horizontal
		display: flex
		align-items: center
		justify-content: space-between
	.thermometer-info
		display: flex
		flex-direction: column
		align-items: center
		margin-top: 20px
		&.horizontal
			margin-top: 0
			text-align: center
		span
			font-size: .8em
			color: #FFFFFF78
			&:first-child
				font-size: 1.2em
				font-weight: bold
				color: white
	.thermometer
		&.vertical
			width: 25px
			height: 200px
			margin-left: 0 auto
			border: 1px solid #393E44
			border-top-left-radius: 20px
			border-top-right-radius: 20px
			border-bottom-left-radius: 20px
			border-bottom-right-radius: 20px
			background-color: #FACD19
			position: relative
			.value
				background-color: #81D648
				border-bottom-left-radius: 20px
				border-bottom-right-radius: 20px
				height: 80%
				position: absolute
				width: 100%
				bottom: 0
				border-top: 4px solid #393E44
				&.full
					height: 100%
					border-top-left-radius: 20px
					border-top-right-radius: 20px
					border-color: transparent
		&.horizontal
			width: 70%
			height: 25px
			margin-left: 10px
			border: 1px solid #393E44
			border-top-left-radius: 20px
			border-top-right-radius: 20px
			border-bottom-left-radius: 20px
			border-bottom-right-radius: 20px
			background-color: #FACD19
			position: relative
			.value
				background-color: #81D648
				border-bottom-left-radius: 20px
				border-top-left-radius: 20px
				height: 100%
				position: absolute
				width: 80%
				bottom: 0
				border-right: 4px solid #393E44			
				

</style>
