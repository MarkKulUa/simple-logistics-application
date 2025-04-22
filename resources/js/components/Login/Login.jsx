import React from 'react';
import { useForm, Controller } from 'react-hook-form';
import axios from "../../axios";

function Login({ isOpen, closeModal }) {
    const { handleSubmit, control, errors } = useForm();

    const onSubmit = async (data) => {
        // Replace this with your login logic
        console.log(data);
        e.preventDefault();
        const { email, password } = e.target.elements;
        const body = {
            email: email.value,
            password: password.value,
        };
        // await csrfToken();
        try {
            const resp = await axios.post('/login', body);
            if (resp.status === 200 && resp.data.user) {
                setUser(resp.data.user);
                return window.location = "/shop";
            }
        } catch (error) {
            if (error.response.status === 401) {
                setError(error.response.data.message);
            }
        }
    };

    return (
        <div
            className={`fixed inset-0 flex items-center justify-center z-50 ${
                isOpen ? '' : 'hidden'
            }`}
        >
            <div className="fixed inset-0 bg-black opacity-50"></div>
            <div className="bg-white w-full md:w-1/3 rounded-lg overflow-hidden">
                <div className="p-6">
                    <h2 className="text-2xl font-semibold mb-4">Login</h2>
                    <form onSubmit={handleSubmit(onSubmit)}>
                        <div className="mb-4">
                            <label className="block mb-2" htmlFor="email">
                                Email
                            </label>
                            <Controller
                                name="email"
                                control={control}
                                rules={{
                                    required: 'Email is required',
                                    pattern: {
                                        value: /^\S+@\S+$/i,
                                        message: 'Invalid email address',
                                    },
                                }}
                                render={({ field }) => (
                                    <input
                                        {...field}
                                        type="text"
                                        id="email"
                                        className={`w-full px-3 py-2 border ${
                                            errors.email ? 'border-red-500' : 'border-gray-300'
                                        } rounded-md focus:outline-none focus:border-blue-500`}
                                    />
                                )}
                            />
                            {errors.email && (
                                <p className="text-red-500 mt-1">{errors.email.message}</p>
                            )}
                        </div>
                        <div className="mb-6">
                            <label className="block mb-2" htmlFor="password">
                                Password
                            </label>
                            <Controller
                                name="password"
                                control={control}
                                rules={{
                                    required: 'Password is required',
                                    minLength: {
                                        value: 6,
                                        message: 'Password must be at least 6 characters',
                                    },
                                }}
                                render={({ field }) => (
                                    <input
                                        {...field}
                                        type="password"
                                        id="password"
                                        className={`w-full px-3 py-2 border ${
                                            errors.password ? 'border-red-500' : 'border-gray-300'
                                        } rounded-md focus:outline-none focus:border-blue-500`}
                                    />
                                )}
                            />
                            {errors.password && (
                                <p className="text-red-500 mt-1">{errors.password.message}</p>
                            )}
                        </div>
                        <div className="text-center">
                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none"
                            >
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default Login;
