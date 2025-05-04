import { Button, Card, Form, Input, Typography, Divider, Space } from 'antd';
import { GoogleOutlined } from '@ant-design/icons';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import { useAuth } from '../../contexts/AuthContext';
import axios from '../../axios';

const Login = () => {
    const [form] = Form.useForm();
    const navigate = useNavigate();
    const { setUser } = useAuth();

    const onFinish = async (values) => {
        try {
            const resp = await axios.post('/login', values);
            if (resp.status === 200 && resp.data.user) {
                setUser(resp.data.user);
                toast.success('Login successful');
                navigate('/shop');
            }
        } catch (err) {
            toast.error(err.response?.data?.message || 'Login failed');
        }
    };

    return (
        <div style={{ minHeight: '60vh' }} className="flex items-center justify-center bg-gray-50 px-4">
            <Card
                title={<Typography.Title level={3} style={{ margin: 0 }}>Login</Typography.Title>}
                bordered={false}
                style={{ width: '100%', maxWidth: 400, boxShadow: '0 4px 12px rgba(0,0,0,0.1)' }}
            >
                <Form form={form} layout="vertical" onFinish={onFinish}>
                    <Form.Item
                        name="email"
                        label="Email"
                        rules={[
                            { required: true, message: 'Please enter your email' },
                            { type: 'email', message: 'Email format is invalid' },
                        ]}
                    >
                        <Input placeholder="Enter your email" />
                    </Form.Item>

                    <Form.Item
                        name="password"
                        label="Password"
                        rules={[
                            { required: true, message: 'Please enter your password' },
                            { min: 6, message: 'Minimum 6 characters' },
                        ]}
                    >
                        <Input.Password placeholder="Enter your password" />
                    </Form.Item>

                    <Form.Item>
                        <Button type="primary" htmlType="submit" block>
                            Login
                        </Button>
                    </Form.Item>
                </Form>

                <Divider plain>OR</Divider>

                <div style={{ textAlign: 'center' }}>
                    <Button
                        icon={<GoogleOutlined />}
                        type="default"
                        disabled
                        block
                    >
                        Sign in with Google (coming soon)
                    </Button>
                </div>
            </Card>
        </div>
    );
};

export default Login;
