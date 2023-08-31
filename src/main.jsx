import React from 'react';
import ReactDOM from 'react-dom/client';
import {Routes, Route, BrowserRouter} from 'react-router-dom';
import {AuthProvider} from './contexts/AuthContext';
import './index.css';
import {Login} from './pages/Login';
import {Welcome} from './pages/Welcome';
import {Shop} from './pages/Shop';
import {Layout} from './components/Layout';
import {NotFoundPage} from './pages/NotFoundPage';
import {Provider} from 'react-redux';
import {setupStore} from './store/store';

const store = setupStore();

ReactDOM.createRoot(document.getElementById('root')).render(
    <Provider store={store}>
        <React.StrictMode>
            <AuthProvider>
                <BrowserRouter>
                    <Routes>
                        <Route path="/" element={<Layout/>}>
                            <Route index element={<Welcome/>}/>
                            <Route path="shop" element={<Shop/>}/>
                            <Route path="login" element={<Login/>}/>
                            <Route path="404" element={<NotFoundPage/>}/>
                        </Route>
                        <Route path="*" element={<NotFoundPage/>}/>
                    </Routes>
                </BrowserRouter>
            </AuthProvider>
        </React.StrictMode>
    </Provider>
);
