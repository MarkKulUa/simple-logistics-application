import { lazy } from 'react';
import AppLayout from './components/AppLayout';
import ProtectedRoute from './components/ProtectedRoute';
import { menuConfig } from './config/menuConfig';

const Login = lazy(() => import('./pages/Login'));
const NotFoundPage = lazy(() => import('./pages/NotFoundPage'));
const pages = {
    Welcome: lazy(() => import('./pages/Welcome')),
    Shop: lazy(() => import('./pages/Shop')),
    Protected: lazy(() => import('./pages/Protected')),
};

const menuRoutes = menuConfig.map(({ path, element }) => {
    const Component = pages[element];
    if (!Component) throw new Error(`"${element}" not found in pages`);
    const isProtected = path === '/protected';

    return {
        path,
        index: path === '/',
        element: isProtected
            ? <ProtectedRoute><Component /></ProtectedRoute>
            : <Component />,
    };
});

export const routes = [
    {
        path: '/',
        element: <AppLayout />,
        children: [
            ...menuRoutes,
            { path: 'login', element: <Login /> },
            { path: '404', element: <NotFoundPage /> },
            { path: '*', element: <NotFoundPage /> },
        ],
    },
];
