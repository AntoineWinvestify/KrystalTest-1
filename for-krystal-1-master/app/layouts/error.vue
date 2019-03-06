<template>
	<v-container grid-list-xs>
		<v-layout row wrap>
			<v-flex class="error-container" xs12>
				<img src="../assets/img/logo.png" alt="Nuxt.js Logo" class="logo" />
				<h1>
					{{ error.statusCode }}
				</h1>
				<h2 class="info">
					{{ error.message }}
				</h2>
				<nuxt-link class="button" :to="`/${$store.state.locale}/dashboard`" v-if="error.statusCode === 404">
					Homepage				
				</nuxt-link>
			</v-flex>
		</v-layout>
	</v-container>
</template>
<script>
export default {
	props: ['error']
}
</script>

<style lang="stylus" scoped>
	.container
		height: 100vh
		background-color: #222529
		h2
			text-align: center
		.error-container
			display: flex
			flex-direction: column
			justify-content: center
			align-items: center
			img
				width: 30%
			a
				text-decoration: none
				color: #73DC50
				font-size: 1.2em
				margin-top: 1em
</style>
