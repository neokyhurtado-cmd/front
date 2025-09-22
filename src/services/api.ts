import axios from 'axios';

const API_BASE_URL = 'https://api.example.com'; // Replace with your actual API base URL

export const fetchContacts = async () => {
    try {
        const response = await axios.get(`${API_BASE_URL}/contacts`);
        return response.data;
    } catch (error) {
        throw new Error('Error fetching contacts: ' + error.message);
    }
};

export const fetchDeals = async () => {
    try {
        const response = await axios.get(`${API_BASE_URL}/deals`);
        return response.data;
    } catch (error) {
        throw new Error('Error fetching deals: ' + error.message);
    }
};

export const createContact = async (contactData) => {
    try {
        const response = await axios.post(`${API_BASE_URL}/contacts`, contactData);
        return response.data;
    } catch (error) {
        throw new Error('Error creating contact: ' + error.message);
    }
};

export const updateContact = async (contactId, contactData) => {
    try {
        const response = await axios.put(`${API_BASE_URL}/contacts/${contactId}`, contactData);
        return response.data;
    } catch (error) {
        throw new Error('Error updating contact: ' + error.message);
    }
};

export const deleteContact = async (contactId) => {
    try {
        await axios.delete(`${API_BASE_URL}/contacts/${contactId}`);
    } catch (error) {
        throw new Error('Error deleting contact: ' + error.message);
    }
};