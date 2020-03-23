const state = {
    loading: false,
    defaultProps: {
        background: '#000', color: 'rgb(255, 255, 255)'
    }
};

const actions = {
    showLoader({ commit }, vs, props = {}) {
        commit('showLoader', vs, props);
    },
    hideLoader({ commit }, vs, props = {}) {
        commit('hideLoader', vs, props);
    }
};

const mutations = {
    showLoader(state, vs, props = state.defaultProps) {
        state.loading = true;
        vs.loading(props)
    },
    hideLoader(state, vs) {
        state.loading = false;
        vs.loading.close()
    },
};

export const loader = {
    namespaced: true,
    state,
    actions,
    mutations
};
