<template>
	<v-container v-resize="resizing" grid-list-md>
		<v-card elevation-1 class="data-card p15" dark>
			<v-layout row wrap>
				<v-flex xs12>
					<div class="card-title button-plus">
						Lending Platforms
						<v-tooltip content-class="winvestify-tooltip" bottom>
							<v-icon
								slot="activator"
							>
								add_circle_outline
							</v-icon>
							<div>
								<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
								<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. At, impedit? Necessitatibus illo nesciunt exercitationem velit debitis aperiam ipsa alias dolor optio, ut maxime, commodi reiciendis iusto recusandae ex vero. Debitis. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt saepe nostrum a eum quibusdam sequi dicta porro ipsam id, culpa totam dolor, alias quisquam atque incidunt possimus, sed ea numquam?</p>
								<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Recusandae animi atque nostrum temporibus placeat aliquid corrupti alias et aperiam repudiandae hic tempora, id culpa. Blanditiis iusto voluptatum odio numquam distinctio.</p>
								<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus vel quod aut harum sint provident vero eligendi ea enim impedit illum consequuntur totam quidem, earum cumque recusandae cum nulla alias. Lorem ipsum dolor sit amet consectetur adipisicing elit. Id, saepe labore ea tenetur tempore voluptatum corrupti numquam, ducimus distinctio provident nulla, sequi cum. Amet, delectus. Soluta officia non nam consequuntur!</p>
							</div>
						</v-tooltip>					
					</div>
				</v-flex>
				<v-flex xs12>
					<v-layout :class="{ 'show': show }" class="filter-mobile" row wrap>
						<v-flex class="custom-checks main" xs12>
							<v-checkbox
								v-model="ex1"
								label="Business Loans"
								color="light-green accent-3"
								value=""
								hide-details
							></v-checkbox>
							<v-checkbox
								v-model="ex2"
								label="Consumer Loans"
								color="light-green accent-3"
								value=""
								hide-details
							></v-checkbox>
							<v-checkbox
								v-model="ex3"
								label="Invoice Financing"
								color="light-green accent-3"
								value=""
								hide-details
							></v-checkbox>
							<v-checkbox
								v-model="ex4"
								label="Mortage Loans"
								color="light-green accent-3"
								value=""
								hide-details
							></v-checkbox>										
						</v-flex>
					</v-layout>
					<v-fab-transition>
						<v-btn
							class="filter-button"
							@click="show = !show"
							v-show="showFilter"
							color="blue accent-4"
							dark
							fixed
							small
							bottom
							right
							fab
						>
							<v-icon>settings</v-icon>
						</v-btn>
					</v-fab-transition>					
				</v-flex>
				<v-flex xs12>
					<v-layout row wrap>
						<v-flex v-for="(item, index) in lendings" :key="index" xs12 sm6 md4 lg4 xl3>
							<lending-item :lending="item" />
						</v-flex>													
					</v-layout>
				</v-flex>
				<v-flex xs12 class="btn-next-container">
					<v-btn nuxt :to="`/${$store.state.locale}/lending-registration/profile`" class="btn-green">Next</v-btn>
				</v-flex>	
			</v-layout>
		</v-card>
	</v-container>
</template>
<script>
	import LendingItem from '~/components/lendings/LendingItem'
	import axios from '~/plugins/axios'
	export default {
		head () {
			return {
				title: 'Winvestify - Lending Registration'
			}
		},
		data () {
			return {
				ex1: false,
				ex2: false,
				ex3: false,
				ex4: false,
				showFilter: false,
				show: false,
				lendings: []
			}
		},
		mounted () {
			this.$store.commit('SET_BUTTON_BACK', false)
			this.getData()
		},
		methods: {
			resizing () {
				if (window.innerWidth <= 800) {
					this.showFilter = true
				} else {
					this.showFilter = false
					this.show = false
				}
			},
			getData () {
				axios.get('/api/companies/lending')
					.then(({ data }) => {
						this.lendings = data
					})
			}
		},
		components: {
			LendingItem
		},
		layout: 'dashboard'
	}
</script>