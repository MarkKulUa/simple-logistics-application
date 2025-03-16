import React, {useEffect, useState} from 'react';
import {Navigate, Outlet} from 'react-router-dom';
import {NavLink} from 'react-router-dom';
import axios from '../axios';
import {useAuth} from '../contexts/AuthContext';

const Layout = () => {
    const {user, setUser} = useAuth();
    const [menuOpen, setMenuOpen] = useState(false);

    // check if user is logged in or not from server
    // useEffect(() => {
    //     // (async () => {
    //     //     try {
    //     //         const resp = await axios.get('/user');
    //     //         if (resp.status === 200) {
    //     //             setUser(resp.data.data);
    //     //         }
    //     //     } catch (error) {
    //     //         if (error.response.status === 401) {
    //     //             localStorage.removeItem('user');
    //     //             window.location.href = '/';
    //     //         }
    //     //     }
    //     // })();
    //
    //     if (user) {
    //      const user = localStorage.setItem('user', JSON.stringify(user));
    //     } else {
    //         localStorage.removeItem('user');
    //     }
    // }, []);

    // if user is not logged in, redirect to login page
    // if (!user) {
    //     return <Navigate to="/" />;
    // }

    return (
        <>
            <nav className="bg-white border-gray-200 px-2 sm:px-4 py-2.5 dark:bg-gray-900">
                <div className="container mx-auto flex items-center justify-between px-4 md:px-8">
                    <a href="/" className="flex items-center">
                        <img
                            src="/favico.svg"
                            className="h-6 mr-3 sm:h-9"
                            alt="Logo"
                        />
                        <span className="self-center text-xl font-semibold whitespace-nowrap dark:text-white">
							Simple Logistics Application
						</span>
                    </a>
                    <button
                        onClick={() => setMenuOpen(!menuOpen)} // Переключение состояния
                        type="button"
                        className="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        aria-controls="navbar-default"
                        aria-expanded={menuOpen}
                    >
                        <span className="sr-only">Open main menu</span>
                        <svg
                            className="w-6 h-6"
                            aria-hidden="true"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                fillRule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clipRule="evenodd"></path>
                        </svg>
                    </button>
                    <div
                        className={`${menuOpen ? 'block' : 'hidden'} absolute top-12 right-4 w-3/4 max-w-[280px] md:static md:block md:w-auto bg-white md:bg-transparent shadow-lg md:shadow-none rounded-lg md:rounded-none`}
                        id="navbar-default"
                    >

                        <ul className="flex flex-col p-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700"
                            onClick={() => setMenuOpen(false)}
                        >
                            {!user && (
                                <li>
                                    <NavLink
                                        to="/login"
                                        className={({isActive}) =>
                                            isActive
                                                ? 'block py-2 pl-3 pr-4 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white'
                                                : 'block py-2 pl-3 pr-4 rounded md:bg-transparent md:p-0 dark:text-gray-400 md:dark:hover:text-white'
                                        }>
                                        Login
                                    </NavLink>
                                </li>
                            )}
                            <li>
                                <NavLink
                                    to="/shop"
                                    className={({isActive}) =>
                                        isActive
                                            ? 'block py-2 pl-3 pr-4 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white'
                                            : 'block py-2 pl-3 pr-4 rounded md:bg-transparent md:p-0 dark:text-gray-400 md:dark:hover:text-white'
                                    }>
                                    Warehouse
                                </NavLink>
                            </li>
                            {user && (
                                <li className="pl-3 pr-4">
                                    {user.name}
                                </li>
                            )}
                        </ul>
                    </div>
                </div>
            </nav>
            <main className="container mx-auto px-4 md:px-8 mt-10 flex flex-col items-center">
                <Outlet/>
            </main>
        </>
    );
}

export default Layout;
