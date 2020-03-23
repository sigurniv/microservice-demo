import { mapState, mapActions } from 'vuex'

export const loaderMixin = {
    computed: {
        ...mapState({
            loading: state => state.loader.loading,
        })
    },
    methods: {
        ...mapActions('loader', ['showLoader', 'hideLoader']),
        startLoader() {
            this.showLoader(this.$vs)
        },
        finishLoader() {
            this.hideLoader(this.$vs)
        }
    },
};
