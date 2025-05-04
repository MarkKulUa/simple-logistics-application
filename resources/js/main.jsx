import React, { Suspense } from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { AuthProvider } from "./contexts/AuthContext";
import Spinner from "./components/Spinner/Spinner";
import { Provider } from "react-redux";
import { setupStore } from "./store/store";
import { routes } from "./routes";
import 'antd/dist/reset.css';
import "../css/index.css";
// import('@vercel/speed-insights').then(({ inject }) => {
//     inject();
// });

const store = setupStore();

ReactDOM.createRoot(document.getElementById("root")).render(
    <Provider store={store}>
        <React.StrictMode>
            <AuthProvider>
                <BrowserRouter>
                    <Suspense fallback={<Spinner />}>
                        <Routes>
                            {routes.map(({ path, element, children }) => (
                                <Route key={path} path={path} element={element}>
                                    {children?.map(({ path, element, index }) => (
                                        <Route key={path || "index"} path={path} element={element} index={index} />
                                    ))}
                                </Route>
                            ))}
                        </Routes>
                    </Suspense>
                </BrowserRouter>
            </AuthProvider>
        </React.StrictMode>
    </Provider>
);
