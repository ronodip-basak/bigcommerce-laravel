const Api = {
    getResource(resource, params) {
        return axios({
            method: 'get',
            url: `/bc-api/${resource}`,
            params,
        });
    },
    
    updateResource(resource, data) {
        return axios({
            method: 'put',
            url: `/bc-api/${resource}`,
            data,
        });
    },
    
    deleteResource(resource, data) {
        return axios({
            method: 'delete',
            url: `/bc-api/${resource}`,
        });
    },
};

export default Api;

