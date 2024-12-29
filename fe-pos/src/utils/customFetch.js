import axios from 'axios';

const customFetch = axios.create({
    baseURL: 'https://backend-website-pos.up.railway.app/api/v1'
  });

  export default customFetch