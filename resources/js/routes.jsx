import { lazy } from 'react';
import AppLayout from './components/AppLayout';
import ProtectedRoute from './components/ProtectedRoute';
import { menuConfig } from './config/menuConfig';

const Login = lazy(() => import('./components/Login/Login'));
const NotFoundPage = lazy(() => import('./pages/NotFoundPage'));
const pages = {
    Welcome: lazy(() => import('./pages/Welcome')),
    Shop: lazy(() => import('./pages/Shop')),
    Protected: lazy(() => import('./pages/Protected')),
    SupportBot: lazy(() => import('./pages/openai/SupportBot')),
    ResumeOptimizer: lazy(() => import('./pages/openai/ResumeOptimizer')),
    CodeReview: lazy(() => import('./pages/openai/CodeReview')),
    EmailWriter: lazy(() => import('./pages/openai/EmailWriter')),
    SeoBlogWriter: lazy(() => import('./pages/openai/SeoBlogWriter')),
    ProductDescriber: lazy(() => import('./pages/openai/ProductDescriber')),
    LanguageCoach: lazy(() => import('./pages/openai/LanguageCoach')),
    Summarizer: lazy(() => import('./pages/openai/Summarizer')),
};

const flattenMenuRoutes = (items) => {
    return items.flatMap(({ path, element, children }) => {
        const Component = pages[element];
        const route = element && Component
            ? {
                path,
                index: path === '/',
                element: path === '/protected'
                    ? <ProtectedRoute><Component /></ProtectedRoute>
                    : <Component />,
            }
            : [];

        const childRoutes = children ? flattenMenuRoutes(children) : [];

        return [route, ...childRoutes];
    });
};

export const routes = [
    {
        path: '/',
        element: <AppLayout />,
        children: [
            ...flattenMenuRoutes(menuConfig),
            { path: 'login', element: <Login /> },
            { path: '404', element: <NotFoundPage /> },
            { path: '*', element: <NotFoundPage /> },
        ],
    },
];
