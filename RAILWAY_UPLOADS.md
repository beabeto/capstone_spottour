# Keep uploaded images on Railway

The app saves image files under `/app/public/uploads`. The Railway service needs a persistent volume mounted at that exact path so images remain available after deployments.

## Configure the volume

1. Open the SpotTour project in Railway and select the Laravel app service.
2. Add a volume to that service and set its mount path to `/app/public/uploads`.
3. Redeploy the service. The startup script creates the image category folders on the mounted volume.
4. Upload a test image, redeploy once more, and confirm it still loads.

If Railway shows a different `RAILWAY_VOLUME_MOUNT_PATH` in the service variables, change the volume mount path to `/app/public/uploads`. The app writes directly to `public/uploads`.

## Existing images

The volume starts empty. Copy any images that currently exist on the live deployment into the volume before relying on it; a new deployment can replace the container filesystem, and mounting a volume over `/app/public/uploads` hides files that were in that directory. Preserve the existing subfolders such as `spots`, `hotels`, `foods`, and `hotel-rooms` when copying.

The image filenames in the database do not need to change as long as the same files are copied into their matching folders.
