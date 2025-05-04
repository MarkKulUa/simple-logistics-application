import { lazy } from 'react';
import AppLayout from './components/AppLayout';
import ProtectedRoute from './components/ProtectedRoute';

const Welcome = lazy(() => import('./pages/Welcome'));
const Shop = lazy(() => import('./pages/Shop'));
const Login = lazy(() => import('./pages/Login'));
// const Signin = lazy(() => import('./pages/Signin'));
const NotFoundPage = lazy(() => import('./pages/NotFoundPage'));

export const routes = [
    {
        path: '/',
        element: <AppLayout />,
        children: [
            { index: true, element: <Welcome /> },
            { path: 'login', element: <Login /> },
            // { path: 'signin', element: <Signin /> },
            { path: 'shop', element: <Shop /> },
            {
                path: 'protected',
                element: (
                    <ProtectedRoute>
                        <div>Here will be Protected content from the route</div>
                    </ProtectedRoute>
                ),
            },
            { path: '404', element: <NotFoundPage /> },
            { path: '*', element: <NotFoundPage /> },
        ],
    },
];
