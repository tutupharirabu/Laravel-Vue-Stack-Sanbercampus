import { ref } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import customFetch from '@/utils/customFetch'

export const useAuthStore = defineStore('auth', () => {
    const router = useRouter()
    const tokenUser = ref(null)
    const currentUser = ref(null)
    const isError = ref(false)
    const errMsg = ref('')

    const loginUser = async (formData) => {
        try {
            isError.value = false;
            errMsg.value = '';
            const { email, password } = formData;

            const { data } = await customFetch.post('/auth/login', {
                email,
                password,
            });

            const { token, user } = data;

            tokenUser.value = token;
            currentUser.value = user;

            // Simpan data ke localStorage
            localStorage.setItem('token', JSON.stringify(token));
            localStorage.setItem('user', JSON.stringify(user));

            if (currentUser.value.role === 'Owner') {
                router.push('/chooseRole');
            } else {
                router.push('/cashier'); // Redirect ke dashboard cashier
            }
        } catch (error) {
            isError.value = true;
            const errors = error.response?.data || { message: 'An error occurred' };

            if (typeof errors === 'object') {
                errMsg.value = Object.values(errors)
                    .flat()
                    .join(', ');
            } else {
                errMsg.value = errors;
            }
        }
    };

    const registerUser = async (formData = {}) => {
        try {
            isError.value = false
            errMsg.value = ''
            const { full_name, email, password, password_confirmation, phone_number } = formData

            const { data } = await customFetch.post('/auth/register', {
                full_name,
                email,
                password,
                password_confirmation,
                phone_number,
            })

            const { token, user } = data

            tokenUser.value = token
            currentUser.value = user

            localStorage.setItem('token', JSON.stringify(token))
            localStorage.setItem('user', JSON.stringify(user))

            router.push('/verifikasiOTPEmail')
        } catch (error) {
            isError.value = true;
            const errors = error.response?.data.error || { message: 'An error occurred' };

            if (typeof errors === 'object') {
                errMsg.value = Object.values(errors)
                    .flat()
                    .join(', ');
            } else {
                errMsg.value = errors;
            }
        }
    }

    const logoutUser = async () => {
        try {
            const { data } = await customFetch.post('/auth/logout', null, {
                headers: {
                    Authorization: `Bearer ${tokenUser.value}`,
                }
            })

            // PINIA: Reset all state values
            tokenUser.value = null
            currentUser.value = null

            // Remove token and user from localStorage
            localStorage.removeItem('token')
            localStorage.removeItem('user')

            router.push('/login')

        } catch (error) {
            isError.value = true;
            const errors = error.response?.data.error || { message: 'An error occurred' };

            if (typeof errors === 'object') {
                errMsg.value = Object.values(errors)
                    .flat()
                    .join(', ');
            } else {
                errMsg.value = errors;
            }
        }
    }

    const getUser = async () => {
        try {
            const { data } = await customFetch.get('/auth/me', {
                headers: {
                    Authorization: `Bearer ${tokenUser.value}`,
                }
            })

            const { data: user } = data

            currentUser.value = user
            localStorage.setItem('user', JSON.stringify(user))

        } catch (error) {
            console.error(error)
        }
    }

    return {
        tokenUser,
        currentUser,
        isError,
        errMsg,
        loginUser,
        registerUser,
        logoutUser,
        getUser,
    }
})