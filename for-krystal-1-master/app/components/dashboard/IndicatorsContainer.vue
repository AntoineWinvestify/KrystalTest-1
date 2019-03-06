<template>
	<v-layout column wrap>
		<v-flex class="section-title" xs12>
			<div class="title-item">
				<v-card-text>{{ name }}</v-card-text>
				<tooltip v-if="iconInfo" small>
					<template slot="content">
						<div>
							<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
						</div>										
					</template>
				</tooltip>
			</div>
			<v-btn icon>
				<v-icon>more_vert</v-icon>
			</v-btn>
		</v-flex>
		<v-layout class="indicators-container" row wrap>
			<slot />
		</v-layout>
	</v-layout>	
</template>
<script>
	import Tooltip from '~/components/ui/Tooltip'
	export default {
		props: {
			name: {
				type: String,
				default: ''
			},
			iconInfo: {
				type: Boolean,
				default: false
			}
		},
		components: {
			Tooltip
		}
	}
</script>