import React, { useState } from 'react';
import Input from '../ui/Input';
import Button from '../ui/Button';

const ContactForm = ({ onSubmit, initialData }) => {
    const [name, setName] = useState(initialData?.name || '');
    const [email, setEmail] = useState(initialData?.email || '');
    const [phone, setPhone] = useState(initialData?.phone || '');

    const handleSubmit = (e) => {
        e.preventDefault();
        onSubmit({ name, email, phone });
    };

    return (
        <form onSubmit={handleSubmit}>
            <Input
                type="text"
                placeholder="Name"
                value={name}
                onChange={(e) => setName(e.target.value)}
            />
            <Input
                type="email"
                placeholder="Email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
            />
            <Input
                type="tel"
                placeholder="Phone"
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
            />
            <Button type="submit">Save Contact</Button>
        </form>
    );
};

export default ContactForm;