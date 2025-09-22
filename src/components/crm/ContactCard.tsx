import React from 'react';

interface ContactCardProps {
    name: string;
    email: string;
    phone: string;
    address: string;
}

const ContactCard: React.FC<ContactCardProps> = ({ name, email, phone, address }) => {
    return (
        <div className="contact-card">
            <h3>{name}</h3>
            <p>Email: {email}</p>
            <p>Phone: {phone}</p>
            <p>Address: {address}</p>
        </div>
    );
};

export default ContactCard;