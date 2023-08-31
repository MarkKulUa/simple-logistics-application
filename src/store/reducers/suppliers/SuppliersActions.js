import axios from "axios";

import { createAsyncThunk } from "@reduxjs/toolkit";

const host = (import.meta.env.VITE_API_URL || "http://localhost:8000/api");
export const fetchSuppliers = createAsyncThunk(
  "suppliers/fetchSuppliers",
  async (limit, thunkAPI) => {
    try {
      const response = await axios.get(
          host +`/suppliers?_limit=${limit}`
      );
      return response.data;
    } catch (error) {
      return thunkAPI.rejectWithValue("No suppliers found");
    }
  }
);

export const createSupplier = createAsyncThunk(
  "suppliers/createSupplier",
  async (newSupplier, thunkAPI) => {
    try {
      const response = await axios.supplier(
          host + "/suppliers",
        newSupplier
      );
      return response.data;
    } catch (error) {
      return thunkAPI.rejectWithValue("No suppliers added");
    }
  }
);

export const updateSupplier = createAsyncThunk(
  "suppliers/updateSupplier",
  async (supplier, thunkAPI) => {
    try {
      const { data } = await axios.put(
        `${host}/suppliers/${supplier.id}`,
        supplier
      );
      return { ...supplier, ...data };
    } catch (error) {
      return thunkAPI.rejectWithValue("No suppliers updated");
    }
  }
);
export const deleteSupplier = createAsyncThunk(
  "suppliers/deleteSupplier",
  async (supplier, thunkAPI) => {
    try {
      const { data } = await axios.delete<ISupplier>(
        `${host}/suppliers/${supplier.id}`
      );
      return { id: supplier.id, data };
    } catch (error) {
      return thunkAPI.rejectWithValue("No suppliers deleted");
    }
  }
);
export const changeSupplierPerPage = createAsyncThunk(
  "suppliers/changeSupplierPerPage",
  async (limit, thunkAPI) => {
    try {
      return limit;
    } catch (error) {
      return thunkAPI.rejectWithValue("No suppliers deleted");
    }
  }
);
