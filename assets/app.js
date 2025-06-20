import './styles/app.css';
import FileUploader from "lib/components/FileUploader";
import {createRoot} from "react-dom/client";

const rootElement = document.getElementById('uploader');
const root = createRoot(rootElement);

root.render(
    <FileUploader />
)


