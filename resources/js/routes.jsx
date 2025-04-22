import { lazy } from "react";
import { Layout } from "./components/Layout";

const Welcome = lazy(() => import("./pages/Welcome"));
const Shop = lazy(() => import("./pages/Shop"));
const Login = lazy(() => import("./pages/Login"));
const NotFoundPage = lazy(() => import("./pages/NotFoundPage"));

export const routes = [
    {
        path: "/",
        element: <Layout />,
        children: [
            { index: true, element: <Welcome /> },
            { path: "shop", element: <Shop /> },
            { path: "login", element: <Login /> },
            { path: "404", element: <NotFoundPage /> },
            { path: "*", element: <NotFoundPage /> },
        ],
    },
    // { path: "*", element: <NotFoundPage /> },
];

