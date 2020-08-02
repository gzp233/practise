package com.cpdms.model.auto;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

public class TsysFileData {
	@JsonSerialize(using= StringFormat.class)
    private String id;
	@JsonSerialize(using= StringFormat.class)
    private String dataId;
	@JsonSerialize(using= StringFormat.class)
    private String fileId;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id == null ? null : id.trim();
    }

    public String getDataId() {
        return dataId;
    }

    public void setDataId(String dataId) {
        this.dataId = dataId == null ? null : dataId.trim();
    }

    public String getFileId() {
        return fileId;
    }

    public void setFileId(String fileId) {
        this.fileId = fileId == null ? null : fileId.trim();
    }
}