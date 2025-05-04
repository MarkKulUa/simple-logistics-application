import React, { useState, useEffect } from 'react';
import { Outlet, useLocation, useNavigate, Navigate } from 'react-router-dom';
import {
    Layout as AntLayout,
    Menu,
    Button,
    Drawer,
    Typography,
    Tooltip,
    Divider,
    Grid,
    Row,
    Col,
    Space,
} from 'antd';
import { MenuOutlined } from '@ant-design/icons';
import { useAuth } from '../contexts/AuthContext';
import axios from '../axios';

const { Header, Content } = AntLayout;
const { useBreakpoint } = Grid;
import { menuConfig } from '../config/menuConfig';

const AppLayout = () => {
    const { user, setUser } = useAuth();
    const navigate = useNavigate();
    const { pathname } = useLocation();
    const [drawerVisible, setDrawerVisible] = useState(false);
    const screens = useBreakpoint();
    const centerMenuItems = menuConfig.map(({ key, label }) => ({
        key,
        label,
    }));

    const handleNav = ({ key }) => {
        setDrawerVisible(false);
        navigate(key);
    };

    const handleLogout = () => {
        console.log('Logout');
    };

    useEffect(() => {
        const fetchUser = async () => {
            try {
                const resp = await axios.get('/user');
                if (resp.status === 200) {
                    setUser(resp.data.data);
                }
            } catch (error) {
                if (error.response?.status === 401) {
                    setUser(null);
                    localStorage.removeItem('user');
                }
            }
        };

        fetchUser();
    }, []);


    return (
        <AntLayout style={{ minHeight: '100vh' }}>
            <Header style={{ background: '#fff', padding: 0, boxShadow: '0 2px 8px #f0f1f2' }}>
                <Row align="middle" justify="space-between" style={{ padding: '0 1rem' }}>
                    {/* Logo */}
                    <Col>
                        <Space align="center" style={{ cursor: 'pointer' }} onClick={() => navigate('/')}>
                            <img src="/favico.svg" alt="Logo" style={{ height: 36 }} />
                            <Tooltip title="Simple Logistics Application">
                                <Typography.Title level={4} style={{ margin: 0 }}>SLA</Typography.Title>
                            </Tooltip>
                        </Space>
                    </Col>

                    {/* Center menu (desktop) */}
                    {screens.md && (
                        <Col flex="auto">
                            <Menu
                                mode="horizontal"
                                items={centerMenuItems}
                                selectedKeys={[pathname === '/' ? '/' : pathname]}
                                onClick={handleNav}
                                style={{ justifyContent: 'center', borderBottom: 'none' }}
                            />
                        </Col>
                    )}

                    {/* Right buttons */}
                    <Col>
                        {screens.md ? (
                            <Space>
                                {!user ? (
                                    <>
                                        <Button type="link" onClick={() => navigate('/login')}>Login</Button>
                                        {/*<Button type="link" onClick={() => navigate('/signin')}>Signin</Button>*/}
                                    </>
                                ) : (
                                    <>
                                        <Typography.Text>{user.name}</Typography.Text>
                                        <Button type="link" onClick={handleLogout}>Logout</Button>
                                    </>
                                )}
                            </Space>
                        ) : (
                            <Button icon={<MenuOutlined />} onClick={() => setDrawerVisible(true)} />
                        )}
                    </Col>
                </Row>
            </Header>

            <Drawer
                placement="right"
                onClose={() => setDrawerVisible(false)}
                open={drawerVisible}
                width={240}
            >
                <Space direction="vertical" style={{ width: '100%' }}>
                    <Menu
                        mode="vertical"
                        items={centerMenuItems}
                        selectedKeys={[pathname === '/' ? '/' : pathname]}
                        onClick={handleNav}
                    />
                    <Divider />
                    {!user ? (
                        <>
                            <Button type="link" block onClick={() => handleNav({ key: '/login' })}>Login</Button>
                            <Button type="link" block onClick={() => handleNav({ key: '/signin' })}>Signin</Button>
                        </>
                    ) : (
                        <>
                            <Typography.Text>{user.name}</Typography.Text>
                            <Button type="link" block onClick={handleLogout}>Logout</Button>
                        </>
                    )}
                </Space>
            </Drawer>

            <Divider style={{ margin: 0 }} />

            <Content style={{ padding: '2rem 1rem' }}>
                <div style={{ maxWidth: 960, margin: '0 auto' }}>
                    <Outlet />
                </div>
            </Content>
        </AntLayout>
    );
};

export default AppLayout;
