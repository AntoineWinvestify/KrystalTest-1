<template>
	<v-layout row wrap>
		<v-flex xs12 md12 lg12>
			<v-card elevation-1  class="data-card">
				<slot name="left" />
			</v-card>
		</v-flex>
		<v-flex xs12 md12 lg12>
			<v-card elevation-1  class="data-card">
				<slot name="rigth" />
			</v-card>
		</v-flex>	
	</v-layout>
</template>